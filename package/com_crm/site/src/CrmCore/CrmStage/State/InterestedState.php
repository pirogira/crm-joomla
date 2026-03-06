<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State;

use Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnectorInterface;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;

final class InterestedState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::INTERESTED;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnectorInterface $db): void
    {
        $this->assertBaseForwardOnly($targetStage);
    }
}

