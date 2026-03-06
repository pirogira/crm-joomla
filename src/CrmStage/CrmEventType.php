<?php
declare(strict_types=1);

namespace Vendor\Crm\CrmStage;

final class CrmEventType
{
    /**
     * Требование: переход в Aware возможен только после события 'разговор с ЛПР'.
     * Это значение должно совпадать с `event_type` в `#__crm_events`.
     */
    public const TALK_WITH_DECISION_MAKER = 'разговор с ЛПР';

    /**
     * Требование: Demo_done требует лога о проведении демо.
     * Это значение должно совпадать с `event_type` в `#__crm_events`.
     */
    public const DEMO_CONDUCTED = 'demo_conducted';
}

