<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State;

use Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnectorInterface;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;

final class DemoPlannedState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::DEMO_PLANNED;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnectorInterface $db): void
    {
        $this->assertBaseForwardOnly($targetStage);

        if (Stage::rank($this->stageName()) < Stage::rank(Stage::AWARE)) {
            throw new TransitionNotAllowedException('Cannot be in Demo_planned below Aware.');
        }
    }
}
