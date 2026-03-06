<?php
declare(strict_types=1);

namespace Vendor\Crm\Tests;

use PHPUnit\Framework\TestCase;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\CrmEventType;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Exceptions\InvalidStageException;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\StageMachine;

/**
 * Unit tests for StageMachine transitions.
 * Uses FakeDatabaseConnector to avoid Joomla/DB dependency.
 */
final class StageMachineTest extends TestCase
{
    private FakeDatabaseConnector $fake;
    private StageMachine $machine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fake = new FakeDatabaseConnector();
        $this->machine = new StageMachine($this->fake);
    }

    public function testTransitionIceToTouchedSucceeds(): void
    {
        $companyId = 1;
        $this->fake->setCompany($companyId, 'Acme', Stage::ICE);

        $this->machine->transition($companyId, Stage::TOUCHED);

        $this->assertTrue($this->fake->wasUpdateCalled());
        $company = $this->fake->getCompanyById($companyId);
        $this->assertSame(Stage::TOUCHED, $company['current_stage']);
    }

    public function testTransitionTouchedToAwareWithoutEventFails(): void
    {
        $companyId = 1;
        $this->fake->setCompany($companyId, 'Acme', Stage::TOUCHED);
        // No "разговор с ЛПР" event

        $this->expectException(TransitionNotAllowedException::class);
        $this->expectExceptionMessage('Aware allowed only after');

        $this->machine->transition($companyId, Stage::AWARE);
    }

    public function testTransitionTouchedToAwareWithEventSucceeds(): void
    {
        $companyId = 1;
        $this->fake->setCompany($companyId, 'Acme', Stage::TOUCHED);
        $this->fake->setHasEvent($companyId, CrmEventType::TALK_WITH_DECISION_MAKER);

        $this->machine->transition($companyId, Stage::AWARE);

        $company = $this->fake->getCompanyById($companyId);
        $this->assertSame(Stage::AWARE, $company['current_stage']);
    }

    public function testTransitionToDemoPlannedFromIceFails(): void
    {
        $companyId = 1;
        $this->fake->setCompany($companyId, 'Acme', Stage::ICE);

        $this->expectException(TransitionNotAllowedException::class);
        $this->expectExceptionMessage('Cannot plan demo when stage is below Aware');

        $this->machine->transition($companyId, Stage::DEMO_PLANNED);
    }

    public function testTransitionToDemoPlannedFromTouchedFails(): void
    {
        $companyId = 1;
        $this->fake->setCompany($companyId, 'Acme', Stage::TOUCHED);
        $this->fake->setHasEvent($companyId, CrmEventType::TALK_WITH_DECISION_MAKER);

        $this->expectException(TransitionNotAllowedException::class);
        $this->expectExceptionMessage('Cannot plan demo when stage is below Aware');

        $this->machine->transition($companyId, Stage::DEMO_PLANNED);
    }

    public function testTransitionToDemoDoneWithoutRecentDemoFails(): void
    {
        $companyId = 1;
        $this->fake->setCompany($companyId, 'Acme', Stage::DEMO_PLANNED);
        // No demo_conducted event in last 60 days
        $since = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P60D'));
        $this->fake->setHasEventSince($companyId, CrmEventType::DEMO_CONDUCTED, $since, false);

        $this->expectException(TransitionNotAllowedException::class);
        $this->expectExceptionMessage('Demo_done requires event');

        $this->machine->transition($companyId, Stage::DEMO_DONE);
    }

    public function testTransitionToDemoDoneWithRecentDemoSucceeds(): void
    {
        $companyId = 1;
        $this->fake->setCompany($companyId, 'Acme', Stage::DEMO_PLANNED);
        $since = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P30D'));
        $this->fake->setHasEventSince($companyId, CrmEventType::DEMO_CONDUCTED, $since, true);

        $this->machine->transition($companyId, Stage::DEMO_DONE);

        $company = $this->fake->getCompanyById($companyId);
        $this->assertSame(Stage::DEMO_DONE, $company['current_stage']);
    }

    public function testTransitionSkipStepFails(): void
    {
        $companyId = 1;
        $this->fake->setCompany($companyId, 'Acme', Stage::ICE);

        $this->expectException(TransitionNotAllowedException::class);
        $this->expectExceptionMessage('Transition Ice -> Aware is not allowed');

        $this->machine->transition($companyId, Stage::AWARE);
    }

    public function testTransitionToInvalidStageThrows(): void
    {
        $companyId = 1;
        $this->fake->setCompany($companyId, 'Acme', Stage::ICE);

        $this->expectException(InvalidStageException::class);
        $this->expectExceptionMessage('Unknown target stage');

        $this->machine->transition($companyId, 'InvalidStage');
    }

    public function testGetCurrentStageThrowsForMissingCompany(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Company not found');

        $this->machine->getCurrentStage(999);
    }

    public function testFullPipelineIceToActivated(): void
    {
        $companyId = 1;
        $this->fake->setCompany($companyId, 'Acme', Stage::ICE);
        $this->fake->setHasEvent($companyId, CrmEventType::TALK_WITH_DECISION_MAKER);
        $since = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P1D'));
        $this->fake->setHasEventSince($companyId, CrmEventType::DEMO_CONDUCTED, $since, true);

        $this->machine->transition($companyId, Stage::TOUCHED);
        $this->machine->transition($companyId, Stage::AWARE);
        $this->machine->transition($companyId, Stage::INTERESTED);
        $this->machine->transition($companyId, Stage::DEMO_PLANNED);
        $this->machine->transition($companyId, Stage::DEMO_DONE);
        $this->machine->transition($companyId, Stage::COMMITTED);
        $this->machine->transition($companyId, Stage::CUSTOMER);
        $this->machine->transition($companyId, Stage::ACTIVATED);

        $company = $this->fake->getCompanyById($companyId);
        $this->assertSame(Stage::ACTIVATED, $company['current_stage']);
    }
}
