<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State;

use Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnectorInterface;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;

final class AwareState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::AWARE;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnectorInterface $db): void
    {
        $this->assertBaseForwardOnly($targetStage);
    }
}

