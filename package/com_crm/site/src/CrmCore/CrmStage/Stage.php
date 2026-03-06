<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage;

final class Stage
{
    public const ICE = 'Ice';
    public const TOUCHED = 'Touched';
    public const AWARE = 'Aware';
    public const INTERESTED = 'Interested';
    public const DEMO_PLANNED = 'Demo_planned';
    public const DEMO_DONE = 'Demo_done';
    public const COMMITTED = 'Committed';
    public const CUSTOMER = 'Customer';
    public const ACTIVATED = 'Activated';

    public const ORDER = [
        self::ICE,
        self::TOUCHED,
        self::AWARE,
        self::INTERESTED,
        self::DEMO_PLANNED,
        self::DEMO_DONE,
        self::COMMITTED,
        self::CUSTOMER,
        self::ACTIVATED,
    ];

    public static function isValid(string $stage): bool
    {
        return in_array($stage, self::ORDER, true);
    }

    public static function rank(string $stage): int
    {
        $idx = array_search($stage, self::ORDER, true);
        if ($idx === false) {
            throw new \InvalidArgumentException('Unknown stage: ' . $stage);
        }
        return (int) $idx;
    }
}

