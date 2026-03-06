<?php
declare(strict_types=1);

namespace Vendor\Crm\CrmStage\State;

use Vendor\Crm\DatabaseConnector;

interface StageStateInterface
{
    public function name(): string;

    /**
     * Throws when transition is forbidden.
     */
    public function assertCanTransitionTo(
        string $targetStage,
        int $companyId,
        DatabaseConnector $db
    ): void;
}

