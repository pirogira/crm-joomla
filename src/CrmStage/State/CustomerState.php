<?php
declare(strict_types=1);

namespace Vendor\Crm\CrmStage\State;

use Vendor\Crm\DatabaseConnector;
use Vendor\Crm\CrmStage\Stage;

final class CustomerState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::CUSTOMER;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnector $db): void
    {
        $this->assertBaseForwardOnly($targetStage);
    }
}

