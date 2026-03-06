<?php
/**
 * Joomla view template: company card
 *
 * Expects:
 * - $this->item   array|null
 * - $this->events array
 * - $this->stage  string
 */
declare(strict_types=1);

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Vendor\Crm\CrmStage\Stage;

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

// Actions available ONLY on the current stage.
// Note: URLs/tasks are examples (под ваши controller tasks).
$actionsByStage = [
    Stage::ICE => [
        ['label' => 'Звонок', 'task' => 'company.logCall', 'btn' => 'btn-primary'],
        ['label' => 'Отметить контакт', 'task' => 'company.touch', 'btn' => 'btn-outline-primary'],
    ],
    Stage::TOUCHED => [
        ['label' => 'Заполнить Discovery', 'task' => 'discovery.create', 'btn' => 'btn-primary'],
        ['label' => 'Разговор с ЛПР', 'task' => 'company.logDecisionMakerTalk', 'btn' => 'btn-outline-primary'],
    ],
    Stage::AWARE => [
        ['label' => 'Уточнить потребность', 'task' => 'company.logNeed', 'btn' => 'btn-primary'],
        ['label' => 'Перевести в Interested', 'task' => 'company.stageInterested', 'btn' => 'btn-outline-primary'],
    ],
    Stage::INTERESTED => [
        ['label' => 'Запланировать демо', 'task' => 'company.stageDemoPlanned', 'btn' => 'btn-warning'],
        ['label' => 'Подтвердить критерии', 'task' => 'company.logCriteria', 'btn' => 'btn-outline-primary'],
    ],
    Stage::DEMO_PLANNED => [
        ['label' => 'Провести демо (лог)', 'task' => 'company.logDemoConducted', 'btn' => 'btn-warning'],
        ['label' => 'Перевести в Demo_done', 'task' => 'company.stageDemoDone', 'btn' => 'btn-outline-warning'],
    ],
    Stage::DEMO_DONE => [
        ['label' => 'Согласовать условия', 'task' => 'company.stageCommitted', 'btn' => 'btn-success'],
    ],
    Stage::COMMITTED => [
        ['label' => 'Оформить как Customer', 'task' => 'company.stageCustomer', 'btn' => 'btn-success'],
    ],
    Stage::CUSTOMER => [
        ['label' => 'Активировать', 'task' => 'company.stageActivated', 'btn' => 'btn-dark'],
    ],
    Stage::ACTIVATED => [
        ['label' => 'Добавить событие', 'task' => 'company.addEvent', 'btn' => 'btn-outline-secondary'],
    ],
];

$instructionByStage = [
    Stage::ICE => "Цель: первый контакт.\n\nСкрипт: представьтесь, уточните роль и договоритесь о следующем шаге.",
    Stage::TOUCHED => "Цель: вывести на осознанность.\n\nСкрипт: коротко зафиксируйте проблему клиента и запросите разговор с ЛПР.",
    Stage::AWARE => "Цель: зафиксировать потребность.\n\nСкрипт: задайте 3–5 вопросов по боли/процессу/критериям успеха.",
    Stage::INTERESTED => "Цель: договориться о демо.\n\nСкрипт: подтвердите кейс и предложите демо под их сценарий.",
    Stage::DEMO_PLANNED => "Цель: подготовить демо.\n\nЧеклист: сценарий, участники, критерии, время, план B.",
    Stage::DEMO_DONE => "Цель: перейти к коммиту.\n\nСкрипт: подведите итоги демо, согласуйте next steps и условия.",
    Stage::COMMITTED => "Цель: закрыть сделку.\n\nСкрипт: финальные согласования, документы, даты, ответственные.",
    Stage::CUSTOMER => "Цель: запустить клиента.\n\nСкрипт: онбординг, план внедрения, контрольные точки.",
    Stage::ACTIVATED => "Цель: удержание и расширение.\n\nСкрипт: успех, кейсы, upsell/cross-sell.",
];

$actions = $actionsByStage[$stage] ?? [];
$instruction = $instructionByStage[$stage] ?? '';

// Base route to controller (пример).
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
                                ?>
                                <button type="submit" name="task" value="<?= htmlspecialchars($task, ENT_QUOTES, 'UTF-8'); ?>"
                                        class="btn <?= htmlspecialchars($btn, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                </button>
                            <?php endforeach; ?>
                            <?= HTMLHelper::_('form.token'); ?>
                        </form>
                        <div class="small text-muted mt-2">
                            Доступные действия зависят от текущей стадии.
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
                    <span class="small text-muted">
                        <?= Text::_('COM_CRM_EVENTS_FROM_DB'); ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if (!$events) : ?>
                        <div class="text-muted">
                            <?= Text::_('COM_CRM_NO_EVENTS'); ?>
                        </div>
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
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?= htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                        </div>
                                        <div class="text-muted small">
                                            #<?= (int) ($e['id'] ?? 0); ?>
                                        </div>
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

