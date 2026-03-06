<?php
declare(strict_types=1);

namespace Vendor\Crm\CrmStage\State;

use Vendor\Crm\DatabaseConnector;
use Vendor\Crm\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\CrmStage\Stage;

final class DemoPlannedState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::DEMO_PLANNED;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnector $db): void
    {
        $this->assertBaseForwardOnly($targetStage);

        // (1) Нельзя планировать демо, если стадия ниже Aware.
        // Этот класс представляет Demo_planned, но constraint применяется на переход В Demo_planned.
        // Поэтому проверка реализована в state источника (InterestedState/AwareState) через базовый граф.
        // Оставим дополнительную защиту на случай неконсистентного использования.
        if (Stage::rank($this->stageName()) < Stage::rank(Stage::AWARE)) {
            throw new TransitionNotAllowedException('Cannot be in Demo_planned below Aware.');
        }
    }
}

