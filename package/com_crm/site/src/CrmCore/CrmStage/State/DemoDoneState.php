<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State;

use Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnectorInterface;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\CrmEventType;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;

final class DemoDoneState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::DEMO_DONE;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnectorInterface $db): void
    {
        $this->assertBaseForwardOnly($targetStage);
    }

    public static function assertDemoRecentlyConducted(int $companyId, DatabaseConnectorInterface $db, int $days = 60): void
    {
        $since = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->sub(new \DateInterval('P' . $days . 'D'));

        if (!$db->hasEventSince($companyId, CrmEventType::DEMO_CONDUCTED, $since)) {
            throw new TransitionNotAllowedException(
                sprintf('Demo_done requires event "%s" not older than %d days.', CrmEventType::DEMO_CONDUCTED, $days)
            );
        }
    }
}
