<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Check requirements
 *
 * Carrot works assuming certain conditions are met. It will
 * quit immediately if they are not.
 *
 */

if (get_magic_quotes_gpc())
{
    exit('Magic quotes are on. Please turn off magic quotes.');
}

if (ini_get('register_globals'))
{
    exit('Register globals are on. Please turn off register globals.');
}

if (version_compare(PHP_VERSION, '5.3.0') < 0)
{
    exit('This framework requires PHP 5.3, please upgrade.');
}

// Load user's autoloader
require __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

// Load dependency registration file paths
require __DIR__ . DIRECTORY_SEPARATOR . 'registrations.php';

if (!isset($registrations) or !$registrations)
{
    $registrations = array();
}

// Instantiate the front controller, dispatch and send the response
$dic = new Carrot\Core\DependencyInjectionContainer($registrations);
$front_controller = $dic->getInstance('\Carrot\Core\FrontController@main');
$response = $front_controller->dispatch();
$response->send();