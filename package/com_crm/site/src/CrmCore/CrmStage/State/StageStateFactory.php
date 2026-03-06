<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State;

use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Exceptions\InvalidStageException;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;

final class StageStateFactory
{
    public static function fromStage(string $stage): StageStateInterface
    {
        return match ($stage) {
            Stage::ICE => new IceState(),
            Stage::TOUCHED => new TouchedState(),
            Stage::AWARE => new AwareState(),
            Stage::INTERESTED => new InterestedState(),
            Stage::DEMO_PLANNED => new DemoPlannedState(),
            Stage::DEMO_DONE => new DemoDoneState(),
            Stage::COMMITTED => new CommittedState(),
            Stage::CUSTOMER => new CustomerState(),
            Stage::ACTIVATED => new ActivatedState(),
            default => throw new InvalidStageException('Unknown stage: ' . $stage),
        };
    }
}
