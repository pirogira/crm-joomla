<?php
declare(strict_types=1);

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\Factory;

$app = Factory::getApplication();
$input = $app->input;

$dispatcher = new ComponentDispatcher($app, $input);
$dispatcher->dispatch();

