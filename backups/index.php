<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

$configFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
$eventsFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'events.php';
$routesFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'routes.php';
$autoloadFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';
$autoloaderClassFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'Carrot' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Autoloader.php';
$systemClassFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'Carrot' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'System.php';

if (!file_exists($systemClassFilePath))
{
    exit("Carrot could not start. '{$systemClassFilePath}' is the wrong path for Carrot\Core\System, was it moved?");
}

require $systemClassFilePath;
$system = new Carrot\Core\System(
    $configFilePath,
    $eventsFilePath,
    $routesFilePath,
    $autoloadFilePath,
    $autoloaderClassFilePath,
    $_SERVER,
    $_GET,
    $_POST,
    $_FILES,
    $_COOKIE,
    $_REQUEST,
    $_ENV
);

$system->reportAllErrors();
$system->checkPHPRequirements();
$system->checkRequiredFileExistence();
$system->initializeAutoloader();
$system->initializeDependencyInjectionContainer();
$system->initializeEventDispatcher();
$system->initializeErrorHandler();
$system->initializeExceptionHandler();
$system->initializeRouter();
$response = $system->run();
$response->send();