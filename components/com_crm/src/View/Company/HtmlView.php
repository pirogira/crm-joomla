<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\View\Company;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Vendor\Crm\CrmStage\Stage;

final class HtmlView extends BaseHtmlView
{
    /** @var array<string,mixed>|null */
    public ?array $item = null;

    /** @var array<int,array<string,mixed>> */
    public array $events = [];

    /** @var string */
    public string $stage = Stage::ICE;

    public function display($tpl = null): void
    {
        $app = Factory::getApplication();
        $companyId = $app->input->getInt('id');

        /** @var \Vendor\Crm\Component\Crm\Site\Model\CompanyModel $model */
        $model = $this->getModel();

        $this->item = $model->getCompany($companyId);
        $this->events = $this->item ? $model->getCompanyEvents($companyId, 200) : [];

        $this->stage = (string) (($this->item['current_stage'] ?? '') ?: Stage::ICE);

        parent::display($tpl);
    }
}

