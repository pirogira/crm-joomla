<?php
declare(strict_types=1);

namespace Vendor\Crm\CrmStage\State;

use Vendor\Crm\CrmStage\Exceptions\TransitionNotAllowedException;
use Vendor\Crm\CrmStage\Stage;

abstract class AbstractStageState implements StageStateInterface
{
    final public function name(): string
    {
        return $this->stageName();
    }

    /**
     * Concrete state stage name.
     */
    abstract protected function stageName(): string;

    /**
     * Default policy: allow only "next" forward step (or same stage).
     * Concrete states can add extra constraints.
     */
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

