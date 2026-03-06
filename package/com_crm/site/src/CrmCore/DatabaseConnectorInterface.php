<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\CrmCore;

interface DatabaseConnectorInterface
{
    public function getCompanyById(int $id): ?array;

    public function updateCompanyStage(int $companyId, string $newStage): void;

    public function hasEvent(int $companyId, string $eventType): bool;

    public function hasEventSince(int $companyId, string $eventType, string|\DateTimeInterface $since): bool;
}
