<?php
declare(strict_types=1);

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Vendor\Crm\Component\Crm\Site\CrmCore\CrmStage\Stage;

HTMLHelper::_('bootstrap.framework');

$company = $this->item;
$events = $this->events ?? [];
$stage = (string) ($this->stage ?? Stage::ICE);

if (!$company) : ?>
    <div class="alert alert-warning">
        <?= Text::_('COM_CRM_COMPANY_NOT_FOUND'); ?>
    </div>
    <?php return; ?>
<?php endif;

$allStages = Stage::ORDER;
$stageRank = Stage::rank($stage);
$progress = (int) round((($stageRank + 1) / max(1, count($allStages))) * 100);

$stageLabels = [
    Stage::ICE => 'Ice',
    Stage::TOUCHED => 'Touched',
    Stage::AWARE => 'Aware',
    Stage::INTERESTED => 'Interested',
    Stage::DEMO_PLANNED => 'Demo planned',
    Stage::DEMO_DONE => 'Demo done',
    Stage::COMMITTED => 'Committed',
    Stage::CUSTOMER => 'Customer',
    Stage::ACTIVATED => 'Activated',
];

$stageBadgeClass = match ($stage) {
    Stage::ICE => 'bg-secondary',
    Stage::TOUCHED => 'bg-info',
    Stage::AWARE, Stage::INTERESTED => 'bg-primary',
    Stage::DEMO_PLANNED, Stage::DEMO_DONE => 'bg-warning text-dark',
    Stage::COMMITTED => 'bg-success',
    Stage::CUSTOMER => 'bg-success',
    Stage::ACTIVATED => 'bg-dark',
    default => 'bg-secondary',
};

$actionsByStage = [
    Stage::ICE => [
        ['label' => Text::_('COM_CRM_ACTION_CALL'), 'task' => 'company.logCall', 'btn' => 'btn-primary'],
        ['label' => Text::_('COM_CRM_ACTION_TOUCH'), 'task' => 'company.touch', 'btn' => 'btn-outline-primary'],
    ],
    Stage::TOUCHED => [
        ['label' => Text::_('COM_CRM_ACTION_DISCOVERY'), 'task' => 'company.addEvent', 'btn' => 'btn-primary', 'event_type' => 'discovery_started'],
        ['label' => Text::_('COM_CRM_ACTION_TALK_DMR'), 'task' => 'company.logDecisionMakerTalk', 'btn' => 'btn-outline-primary'],
    ],
    Stage::AWARE => [
        ['label' => Text::_('COM_CRM_ACTION_STAGE_INTERESTED'), 'task' => 'company.stageInterested', 'btn' => 'btn-outline-primary'],
    ],
    Stage::INTERESTED => [
        ['label' => Text::_('COM_CRM_ACTION_STAGE_DEMO_PLANNED'), 'task' => 'company.stageDemoPlanned', 'btn' => 'btn-warning'],
    ],
    Stage::DEMO_PLANNED => [
        ['label' => Text::_('COM_CRM_ACTION_LOG_DEMO'), 'task' => 'company.logDemoConducted', 'btn' => 'btn-warning'],
        ['label' => Text::_('COM_CRM_ACTION_STAGE_DEMO_DONE'), 'task' => 'company.stageDemoDone', 'btn' => 'btn-outline-warning'],
    ],
    Stage::DEMO_DONE => [
        ['label' => Text::_('COM_CRM_ACTION_STAGE_COMMITTED'), 'task' => 'company.stageCommitted', 'btn' => 'btn-success'],
    ],
    Stage::COMMITTED => [
        ['label' => Text::_('COM_CRM_ACTION_STAGE_CUSTOMER'), 'task' => 'company.stageCustomer', 'btn' => 'btn-success'],
    ],
    Stage::CUSTOMER => [
        ['label' => Text::_('COM_CRM_ACTION_STAGE_ACTIVATED'), 'task' => 'company.stageActivated', 'btn' => 'btn-dark'],
    ],
    Stage::ACTIVATED => [
        ['label' => Text::_('COM_CRM_ACTION_ADD_EVENT'), 'task' => 'company.addEvent', 'btn' => 'btn-outline-secondary', 'event_type' => 'note'],
    ],
];

$instructionByStage = [
    Stage::ICE => Text::_('COM_CRM_SCRIPT_ICE'),
    Stage::TOUCHED => Text::_('COM_CRM_SCRIPT_TOUCHED'),
    Stage::AWARE => Text::_('COM_CRM_SCRIPT_AWARE'),
    Stage::INTERESTED => Text::_('COM_CRM_SCRIPT_INTERESTED'),
    Stage::DEMO_PLANNED => Text::_('COM_CRM_SCRIPT_DEMO_PLANNED'),
    Stage::DEMO_DONE => Text::_('COM_CRM_SCRIPT_DEMO_DONE'),
    Stage::COMMITTED => Text::_('COM_CRM_SCRIPT_COMMITTED'),
    Stage::CUSTOMER => Text::_('COM_CRM_SCRIPT_CUSTOMER'),
    Stage::ACTIVATED => Text::_('COM_CRM_SCRIPT_ACTIVATED'),
];

$actions = $actionsByStage[$stage] ?? [];
$instruction = (string) ($instructionByStage[$stage] ?? '');

$formAction = Route::_('index.php?option=com_crm&view=company&id=' . (int) $company['id']);
?>

<div class="crm-company-card container-fluid">
    <div class="row g-3">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h2 class="m-0">
                        <?= htmlspecialchars((string) ($company['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                    <div class="text-muted small">
                        ID: <?= (int) ($company['id'] ?? 0); ?> ·
                        <?= Text::_('COM_CRM_CREATED_AT'); ?>:
                        <?= htmlspecialchars((string) ($company['created_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>

                <div class="text-end">
                    <span class="badge <?= $stageBadgeClass; ?> fs-6">
                        <?= htmlspecialchars($stageLabels[$stage] ?? $stage, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <div class="mt-2" style="min-width: 220px;">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar"
                                 style="width: <?= $progress; ?>%;"
                                 aria-valuenow="<?= $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="small text-muted mt-1">
                            <?= (int) $progress; ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <?= Text::_('COM_CRM_ACTIONS'); ?>
                </div>
                <div class="card-body">
                    <?php if (!$actions) : ?>
                        <div class="text-muted">
                            <?= Text::_('COM_CRM_NO_ACTIONS_FOR_STAGE'); ?>
                        </div>
                    <?php else : ?>
                        <form method="post" action="<?= $formAction; ?>" class="d-grid gap-2">
                            <?php foreach ($actions as $a) :
                                $task = (string) ($a['task'] ?? '');
                                $label = (string) ($a['label'] ?? '');
                                $btn = (string) ($a['btn'] ?? 'btn-secondary');
                                $eventType = (string) ($a['event_type'] ?? '');
                                ?>
                                <button type="submit"
                                        class="btn <?= htmlspecialchars($btn, ENT_QUOTES, 'UTF-8'); ?>"
                                        name="task"
                                        value="<?= htmlspecialchars($task, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                </button>
                                <?php if ($task === 'company.addEvent' && $eventType !== '') : ?>
                                    <input type="hidden" name="event_type" value="<?= htmlspecialchars($eventType, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?= HTMLHelper::_('form.token'); ?>
                        </form>
                        <div class="small text-muted mt-2">
                            <?= Text::_('COM_CRM_ACTIONS_DEPEND_ON_STAGE'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <?= Text::_('COM_CRM_INSTRUCTION_SCRIPT'); ?>
                </div>
                <div class="card-body">
                    <pre class="m-0" style="white-space: pre-wrap; font-family: inherit;"><?= htmlspecialchars($instruction, ENT_QUOTES, 'UTF-8'); ?></pre>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span><?= Text::_('COM_CRM_EVENT_HISTORY'); ?></span>
                    <span class="small text-muted"><?= Text::_('COM_CRM_EVENTS_FROM_DB'); ?></span>
                </div>
                <div class="card-body">
                    <?php if (!$events) : ?>
                        <div class="text-muted"><?= Text::_('COM_CRM_NO_EVENTS'); ?></div>
                    <?php else : ?>
                        <div class="list-group">
                            <?php foreach ($events as $e) :
                                $createdAt = (string) ($e['created_at'] ?? '');
                                $type = (string) ($e['event_type'] ?? '');
                                $payloadRaw = (string) ($e['payload'] ?? '');
                                $payload = null;
                                if ($payloadRaw !== '') {
                                    $decoded = json_decode($payloadRaw, true);
                                    $payload = is_array($decoded) ? $decoded : null;
                                }
                                ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="text-muted small">#<?= (int) ($e['id'] ?? 0); ?></div>
                                    </div>

                                    <?php if ($payload !== null) : ?>
                                        <details class="mt-2">
                                            <summary class="small text-muted">payload</summary>
                                            <pre class="mb-0 mt-2 bg-light p-2 rounded" style="overflow:auto;"><?= htmlspecialchars(
                                                json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?></pre>
                                        </details>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

