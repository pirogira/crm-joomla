<?php
declare(strict_types=1);

namespace Vendor\Crm\CrmStage\State;

use Vendor\Crm\DatabaseConnector;
use Vendor\Crm\CrmStage\Stage;

final class IceState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::ICE;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnector $db): void
    {
        $this->assertBaseForwardOnly($targetStage);
    }
}

