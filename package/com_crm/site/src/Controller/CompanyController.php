<?php
declare(strict_types=1);

namespace Vendor\Crm\Component\Crm\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\CrmEventType;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\StageMachine;
use Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnector;

final class CompanyController extends FormController
{
    /**
     * Generic helper: redirect back to company card.
     */
    private function redirectToCompany(int $companyId): void
    {
        $this->setRedirect(Route::_('index.php?option=com_crm&view=company&id=' . $companyId, false));
    }

    private function dbx(): \Vendor\Crm\Component\Crm\Site\CrmCore\DatabaseConnector
    {
        return new DatabaseConnector(Factory::getContainer()->get('DatabaseDriver'));
    }

    private function stageMachine(): StageMachine
    {
        return new StageMachine($this->dbx());
    }

    public function touch(): bool
    {
        $companyId = $this->input->getInt('id');
        $this->stageMachine()->transition($companyId, Stage::TOUCHED);
        $this->redirectToCompany($companyId);
        return true;
    }

    public function logCall(): bool
    {
        $companyId = $this->input->getInt('id');
        $this->dbx()->addCrmEvent($companyId, 'call', ['source' => 'ui']);
        $this->redirectToCompany($companyId);
        return true;
    }

    public function logDecisionMakerTalk(): bool
    {
        $companyId = $this->input->getInt('id');
        $this->dbx()->addCrmEvent($companyId, CrmEventType::TALK_WITH_DECISION_MAKER, ['source' => 'ui']);
        $this->redirectToCompany($companyId);
        return true;
    }

    public function stageInterested(): bool
    {
        $companyId = $this->input->getInt('id');
        $this->stageMachine()->transition($companyId, Stage::INTERESTED);
        $this->redirectToCompany($companyId);
        return true;
    }

    public function stageDemoPlanned(): bool
    {
        $companyId = $this->input->getInt('id');
        $this->stageMachine()->transition($companyId, Stage::DEMO_PLANNED);
        $this->redirectToCompany($companyId);
        return true;
    }

    public function logDemoConducted(): bool
    {
        $companyId = $this->input->getInt('id');
        $this->dbx()->addCrmEvent($companyId, CrmEventType::DEMO_CONDUCTED, ['source' => 'ui']);
        $this->redirectToCompany($companyId);
        return true;
    }

    public function stageDemoDone(): bool
    {
        $companyId = $this->input->getInt('id');
        $this->stageMachine()->transition($companyId, Stage::DEMO_DONE);
        $this->redirectToCompany($companyId);
        return true;
    }

    public function stageCommitted(): bool
    {
        $companyId = $this->input->getInt('id');
        $this->stageMachine()->transition($companyId, Stage::COMMITTED);
        $this->redirectToCompany($companyId);
        return true;
    }

    public function stageCustomer(): bool
    {
        $companyId = $this->input->getInt('id');
        $this->stageMachine()->transition($companyId, Stage::CUSTOMER);
        $this->redirectToCompany($companyId);
        return true;
    }

    public function stageActivated(): bool
    {
        $companyId = $this->input->getInt('id');
        $this->stageMachine()->transition($companyId, Stage::ACTIVATED);
        $this->redirectToCompany($companyId);
        return true;
    }

    public function addEvent(): bool
    {
        $companyId = $this->input->getInt('id');
        $eventType = (string) $this->input->getCmd('event_type', 'note');
        $payload = ['source' => 'ui'];

        $this->dbx()->addCrmEvent($companyId, $eventType, $payload);
        $this->redirectToCompany($companyId);
        return true;
    }
}

