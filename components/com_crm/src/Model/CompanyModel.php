<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Vendor\Crm\DatabaseConnector;

final class CompanyModel extends BaseDatabaseModel
{
    public function getCompany(int $companyId): ?array
    {
        $dbx = new DatabaseConnector(Factory::getContainer()->get('DatabaseDriver'));
        return $dbx->getCompanyById($companyId);
    }

    public function getCompanyEvents(int $companyId, int $limit = 200): array
    {
        $dbx = new DatabaseConnector(Factory::getContainer()->get('DatabaseDriver'));
        return $dbx->getEventsForCompany($companyId, $limit, 0);
    }
}

