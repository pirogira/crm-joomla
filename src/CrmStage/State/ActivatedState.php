<?php
declare(strict_types=1);

namespace Vendor\Crm\CrmStage\State;

use Vendor\Crm\DatabaseConnector;
use Vendor\Crm\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\CrmStage\Stage;

final class ActivatedState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::ACTIVATED;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnector $db): void
    {
        // Terminal state: can only stay here.
        if ($targetStage !== Stage::ACTIVATED) {
            throw new TransitionNotAllowedException('Activated is a terminal stage.');
        }
    }
}

