<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State;

use Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnectorInterface;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;

final class ActivatedState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::ACTIVATED;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnectorInterface $db): void
    {
        if ($targetStage !== Stage::ACTIVATED) {
            throw new TransitionNotAllowedException('Activated is a terminal stage.');
        }
    }
}
