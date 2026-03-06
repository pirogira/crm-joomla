<?php
declare(strict_types=1);

namespace Vendor\Crm\Tests;

use Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnectorInterface;

/**
 * Test double for DatabaseConnector. Configurable return values for unit tests.
 */
final class FakeDatabaseConnector implements DatabaseConnectorInterface
{
    /** @var array<int, array{id: int, name: string, current_stage: string}> */
    private array $companies = [];

    /** @var array<string, bool> "companyId:eventType" => true */
    private array $hasEvent = [];

    /** @var array<string, bool> "companyId:eventType:since" => true */
    private array $hasEventSince = [];

    private bool $updateCalled = false;

    public function setCompany(int $id, string $name, string $currentStage): void
    {
        $this->companies[$id] = [
            'id' => $id,
            'name' => $name,
            'current_stage' => $currentStage,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function setHasEvent(int $companyId, string $eventType, bool $value = true): void
    {
        $this->hasEvent[$companyId . ':' . $eventType] = $value;
    }

    public function setHasEventSince(int $companyId, string $eventType, string|\DateTimeInterface $since, bool $value = true): void
    {
        $this->hasEventSince[$companyId . ':' . $eventType] = $value;
    }

    public function wasUpdateCalled(): bool
    {
        return $this->updateCalled;
    }

    public function getCompanyById(int $id): ?array
    {
        return $this->companies[$id] ?? null;
    }

    public function updateCompanyStage(int $companyId, string $newStage): void
    {
        $this->updateCalled = true;
        if (isset($this->companies[$companyId])) {
            $this->companies[$companyId]['current_stage'] = $newStage;
        }
    }

    public function hasEvent(int $companyId, string $eventType): bool
    {
        $key = $companyId . ':' . $eventType;
        return $this->hasEvent[$key] ?? false;
    }

    public function hasEventSince(int $companyId, string $eventType, string|\DateTimeInterface $since): bool
    {
        $key = $companyId . ':' . $eventType;
        return $this->hasEventSince[$key] ?? false;
    }
}
