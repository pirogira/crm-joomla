<?php
declare(strict_types=1);

namespace Vendor\Crm;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;

final class DatabaseConnector
{
    private DatabaseDriver $db;

    public function __construct(?DatabaseDriver $db = null)
    {
        // Joomla 4/5: get DatabaseDriver from DI container
        $this->db = $db ?? Factory::getContainer()->get('DatabaseDriver');
    }

    /* ---------------- Companies ---------------- */

    public function createCompany(string $name, string $currentStage): int
    {
        $query = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__companies'))
            ->columns([
                $this->db->quoteName('name'),
                $this->db->quoteName('current_stage'),
                $this->db->quoteName('created_at'),
            ])
            ->values(implode(',', [
                ':name',
                ':current_stage',
                'CURRENT_TIMESTAMP(3)',
            ]))
            ->bind(':name', $name)
            ->bind(':current_stage', $currentStage);

        $this->db->setQuery($query)->execute();

        return (int) $this->db->insertid();
    }

    public function getCompanyById(int $id): ?array
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__companies'))
            ->where($this->db->quoteName('id') . ' = :id')
            ->bind(':id', $id, \PDO::PARAM_INT);

        $this->db->setQuery($query);
        $row = $this->db->loadAssoc();

        return $row ?: null;
    }

    public function listCompaniesByStage(string $stage, int $limit = 50, int $offset = 0): array
    {
        $limit = max(1, min(500, $limit));
        $offset = max(0, $offset);

        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__companies'))
            ->where($this->db->quoteName('current_stage') . ' = :stage')
            ->order($this->db->quoteName('created_at') . ' DESC')
            ->bind(':stage', $stage);

        $this->db->setQuery($query, $offset, $limit);

        return (array) $this->db->loadAssocList();
    }

    public function updateCompanyStage(int $companyId, string $newStage): void
    {
        $query = $this->db->getQuery(true)
            ->update($this->db->quoteName('#__companies'))
            ->set($this->db->quoteName('current_stage') . ' = :stage')
            ->where($this->db->quoteName('id') . ' = :id')
            ->bind(':stage', $newStage)
            ->bind(':id', $companyId, \PDO::PARAM_INT);

        $this->db->setQuery($query)->execute();
    }

    /* ---------------- CRM events ---------------- */

    public function addCrmEvent(int $companyId, string $eventType, array $payload): int
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new \RuntimeException('Failed to encode payload to JSON.');
        }

        $query = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__crm_events'))
            ->columns([
                $this->db->quoteName('company_id'),
                $this->db->quoteName('event_type'),
                $this->db->quoteName('payload'),
                $this->db->quoteName('created_at'),
            ])
            ->values(implode(',', [
                ':company_id',
                ':event_type',
                ':payload',
                'CURRENT_TIMESTAMP(3)',
            ]))
            ->bind(':company_id', $companyId, \PDO::PARAM_INT)
            ->bind(':event_type', $eventType)
            ->bind(':payload', $json);

        $this->db->setQuery($query)->execute();

        return (int) $this->db->insertid();
    }

    public function getEventsForCompany(int $companyId, int $limit = 100, int $offset = 0): array
    {
        $limit = max(1, min(1000, $limit));
        $offset = max(0, $offset);

        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__crm_events'))
            ->where($this->db->quoteName('company_id') . ' = :company_id')
            ->order($this->db->quoteName('created_at') . ' DESC')
            ->bind(':company_id', $companyId, \PDO::PARAM_INT);

        $this->db->setQuery($query, $offset, $limit);

        return (array) $this->db->loadAssocList();
    }

    public function hasEvent(int $companyId, string $eventType): bool
    {
        $query = $this->db->getQuery(true)
            ->select('1')
            ->from($this->db->quoteName('#__crm_events'))
            ->where($this->db->quoteName('company_id') . ' = :company_id')
            ->where($this->db->quoteName('event_type') . ' = :event_type')
            ->bind(':company_id', $companyId, \PDO::PARAM_INT)
            ->bind(':event_type', $eventType);

        $this->db->setQuery($query, 0, 1);

        return (bool) $this->db->loadResult();
    }

    /**
     * @param string|\DateTimeInterface $since Inclusive lower bound.
     */
    public function hasEventSince(int $companyId, string $eventType, string|\DateTimeInterface $since): bool
    {
        $sinceStr = $since instanceof \DateTimeInterface
            ? $since->format('Y-m-d H:i:s.u')
            : $since;

        $query = $this->db->getQuery(true)
            ->select('1')
            ->from($this->db->quoteName('#__crm_events'))
            ->where($this->db->quoteName('company_id') . ' = :company_id')
            ->where($this->db->quoteName('event_type') . ' = :event_type')
            ->where($this->db->quoteName('created_at') . ' >= :since')
            ->bind(':company_id', $companyId, \PDO::PARAM_INT)
            ->bind(':event_type', $eventType)
            ->bind(':since', $sinceStr);

        $this->db->setQuery($query, 0, 1);

        return (bool) $this->db->loadResult();
    }

    /* ---------------- Discovery forms ---------------- */

    public function saveDiscoveryForm(?int $companyId, string $formKey, array $data): int
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new \RuntimeException('Failed to encode discovery form data to JSON.');
        }

        $query = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__discovery_forms'))
            ->columns([
                $this->db->quoteName('company_id'),
                $this->db->quoteName('form_key'),
                $this->db->quoteName('data'),
                $this->db->quoteName('created_at'),
            ])
            ->values(implode(',', [
                ':company_id',
                ':form_key',
                ':data',
                'CURRENT_TIMESTAMP(3)',
            ]))
            ->bind(':company_id', $companyId, $companyId === null ? \PDO::PARAM_NULL : \PDO::PARAM_INT)
            ->bind(':form_key', $formKey)
            ->bind(':data', $json);

        $this->db->setQuery($query)->execute();

        return (int) $this->db->insertid();
    }

    public function getDiscoveryFormsByCompany(int $companyId, int $limit = 50, int $offset = 0): array
    {
        $limit = max(1, min(500, $limit));
        $offset = max(0, $offset);

        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__discovery_forms'))
            ->where($this->db->quoteName('company_id') . ' = :company_id')
            ->order($this->db->quoteName('created_at') . ' DESC')
            ->bind(':company_id', $companyId, \PDO::PARAM_INT);

        $this->db->setQuery($query, $offset, $limit);

        return (array) $this->db->loadAssocList();
    }
}

