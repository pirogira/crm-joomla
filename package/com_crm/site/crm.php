<?php
declare(strict_types=1);

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\Factory;

// Minimal entry point for Joomla 4/5 component.
// Uses core ComponentDispatcher which will resolve controllers by convention.

$app = Factory::getApplication();
$input = $app->input;

$dispatcher = new ComponentDispatcher($app, $input);
$dispatcher->dispatch();

