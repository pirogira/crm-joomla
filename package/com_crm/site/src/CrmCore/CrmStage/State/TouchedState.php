<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State;

use Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnectorInterface;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\CrmEventType;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;

final class TouchedState extends AbstractStageState
{
    protected function stageName(): string
    {
        return Stage::TOUCHED;
    }

    public function assertCanTransitionTo(string $targetStage, int $companyId, DatabaseConnectorInterface $db): void
    {
        $this->assertBaseForwardOnly($targetStage);

        if ($targetStage === Stage::AWARE) {
            if (!$db->hasEvent($companyId, CrmEventType::TALK_WITH_DECISION_MAKER)) {
                throw new TransitionNotAllowedException(
                    "Aware allowed only after event '" . CrmEventType::TALK_WITH_DECISION_MAKER . "'."
                );
            }
        }
    }
}

