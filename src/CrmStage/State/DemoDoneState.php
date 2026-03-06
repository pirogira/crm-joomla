<?php
declare(strict_types=1);

namespace Vendor\Crm\CrmStage\State;

use Vendor\Crm\DatabaseConnector;
use Vendor\Crm\CrmStage\CrmEventType;
use Vendor\Crm\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\CrmStage\Stage;

final class DemoDoneState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::DEMO_DONE;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnector $db): void
    {
        $this->assertBaseForwardOnly($targetStage);

        // Nothing extra for moving forward from Demo_done (constraints apply when entering Demo_done).
    }

    /**
     * Constraint when entering Demo_done.
     */
    public static function assertDemoRecentlyConducted(int $companyId, DatabaseConnector $db, int $days = 60): void
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

