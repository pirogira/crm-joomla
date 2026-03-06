<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State;

use Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnectorInterface;

interface StageStateInterface
{
    public function name(): string;

    public function assertCanTransitionTo(
        string $targetStage,
        int $companyId,
        DatabaseConnectorInterface $db
    ): void;
}

