<?php
declare(strict_types=1);

namespace Vendor\Crm\CrmStage;

use Vendor\Crm\CrmStage\Exceptions\InvalidStageException;
use Vendor\Crm\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\CrmStage\State\DemoDoneState;
use Vendor\Crm\CrmStage\State\StageStateFactory;
use Vendor\Crm\DatabaseConnector;

/**
 * State-pattern based stage transitions for CRM.
 *
 * - Current stage is stored in `#__companies.current_stage`
 * - All transition constraints are validated ONLY via `#__crm_events`
 */
final class StageMachine
{
    public function __construct(private readonly DatabaseConnector $db)
    {
    }

    public function getCurrentStage(int $companyId): string
    {
        $company = $this->db->getCompanyById($companyId);
        if (!$company) {
            throw new \RuntimeException('Company not found: ' . $companyId);
        }

        $stage = (string) ($company['current_stage'] ?? '');
        if (!Stage::isValid($stage)) {
            throw new InvalidStageException('Invalid current_stage in DB: ' . $stage);
        }

        return $stage;
    }

    /**
     * Attempts to move company to $targetStage and persists `current_stage`.
     * Throws TransitionNotAllowedException if constraints are not met.
     */
    public function transition(int $companyId, string $targetStage): void
    {
        if (!Stage::isValid($targetStage)) {
            throw new InvalidStageException('Unknown target stage: ' . $targetStage);
        }

        $currentStage = $this->getCurrentStage($companyId);

        // Global constraint (1): cannot plan demo if stage ниже Aware.
        if ($targetStage === Stage::DEMO_PLANNED && Stage::rank($currentStage) < Stage::rank(Stage::AWARE)) {
            throw new TransitionNotAllowedException('Cannot plan demo when stage is below Aware.');
        }

        // Constraint (3) applies when ENTERING Demo_done (target).
        if ($targetStage === Stage::DEMO_DONE) {
            DemoDoneState::assertDemoRecentlyConducted($companyId, $this->db, 60);
        }

        // State-specific + base graph constraints.
        $state = StageStateFactory::fromStage($currentStage);
        $state->assertCanTransitionTo($targetStage, $companyId, $this->db);

        // If all checks passed, persist stage.
        $this->db->updateCompanyStage($companyId, $targetStage);
    }
}

