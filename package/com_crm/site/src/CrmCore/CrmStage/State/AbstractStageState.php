<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\State;

use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;

abstract class AbstractStageState implements StageStateInterface
{
    final public function name(): string
    {
        return $this->stageName();
    }

    abstract protected function stageName(): string;

    protected function assertBaseForwardOnly(string $targetStage): void
    {
        if (!Stage::isValid($targetStage)) {
            throw new TransitionNotAllowedException('Unknown target stage: ' . $targetStage);
        }

        $from = Stage::rank($this->stageName());
        $to = Stage::rank($targetStage);

        if ($to === $from) {
            return;
        }

        if ($to !== $from + 1) {
            throw new TransitionNotAllowedException(
                sprintf('Transition %s -> %s is not allowed.', $this->stageName(), $targetStage)
            );
        }
    }
}

