<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage;

use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Exceptions\InvalidStageException;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State\DemoDoneState;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State\StageStateFactory;
use Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnectorInterface;

/**
 * State-pattern based stage transitions for CRM.
 *
 * - Current stage is stored in `#__companies.current_stage`
 * - All transition constraints are validated ONLY via `#__crm_events`
 */
final class StageMachine
{
    public function __construct(private readonly DatabaseConnectorInterface $db)
    {
    }

    public function getCurrentStage(int $companyId): string
    {
        $company = $this->db->getCompanyById($companyId);
        if (!$company) {
            throw new \RuntimeException('Company not found: ' . $companyId);
        }

        $stage = (string) ($company['current_stage'] ?? '');
        if (!Stage::isValid($stage)) {
            throw new InvalidStageException('Invalid current_stage in DB: ' . $stage);
        }

        return $stage;
    }

    /**
     * Attempts to move company to $targetStage and persists `current_stage`.
     * Throws TransitionNotAllowedException if constraints are not met.
     */
    public function transition(int $companyId, string $targetStage): void
    {
        if (!Stage::isValid($targetStage)) {
            throw new InvalidStageException('Unknown target stage: ' . $targetStage);
        }

        $currentStage = $this->getCurrentStage($companyId);

        if ($targetStage === Stage::DEMO_PLANNED && Stage::rank($currentStage) < Stage::rank(Stage::AWARE)) {
            throw new TransitionNotAllowedException('Cannot plan demo when stage is below Aware.');
        }

        if ($targetStage === Stage::DEMO_DONE) {
            DemoDoneState::assertDemoRecentlyConducted($companyId, $this->db, 60);
        }

        $state = StageStateFactory::fromStage($currentStage);
        $state->assertCanTransitionTo($targetStage, $companyId, $this->db);

        $this->db->updateCompanyStage($companyId, $targetStage);
    }
}
