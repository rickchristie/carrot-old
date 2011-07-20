<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>
 *
 * Licensed under the MIT License.
 *
 */

use Carrot\Core\ObjectReference;

/**
 * Carrot works assuming certain conditions are met. It will quit
 * immediately if they are not. Although this seems like a
 * childish behavior, it's actually quite an effective way to
 * ensure that the framework works as expected.
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

/**
 * We advocate strict coding, thus this framework will report all
 * errors, even the tiniest mistakes.
 */

error_reporting(E_ALL | E_STRICT);

/**
 * Geting the autoloader up and running. Loads user's autoload.php
 * file.
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Instantiates Dependency Injection Container, the core part of
 * Carrot that allows wiring of dependencies.
 */

$dic = new Carrot\Core\DependencyInjectionContainer;

/**
// ---------------------------------------------------------------
 * Registers the default bindings for the dependency injection
 * container. You can override these settings later on
 *
 */

$dic->bind('Carrot\Core\AppRequestURI{Main:Transient}', array(
    $_SERVER['SCRIPT_NAME'],
    $_SERVER['REQUEST_URI']
));

$dic->bind('Carrot\Core\ExceptionHandler{Main:Transient}', array(
    $_SERVER['SERVER_PROTOCOL']
));

$dic->bind('Carrot\Core\Request{Main:Singleton}', array(
    $_SERVER,
    $_GET,
    $_POST,
    $_FILES,
    $_COOKIE,
    $_REQUEST,
    $_ENV
));

$dic->bind('Carrot\Core\FrontController{Main:Transient}', array(
    $_SERVER['SERVER_PROTOCOL']
));

/**
 * Change error messages into ErrorException, and set the
 * exception handler. You can change the templates loaded when an
 * exception occurs by injecting different ExceptionPageInterface
 * implementation in the appropriate provider class.
 */

$errorHandler = function($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
};

set_error_handler($errorHandler);
$exceptionHandler = $dic->getInstance(new Carrot\Core\ObjectReference('Carrot\Core\ExceptionHandler{Main:Transient}'));
$exceptionHandler->set();

/**
// ---------------------------------------------------------------
 * Loads the user configuration file 
 *
 */

$dic->loadConfigurationFile(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');

/**
 * Loads the router
 *
 */

$router = $dic->getInstance(new ObjectReference('Carrot\Core\Router{Main:Singleton}'));
$router->loadConfigurationFile(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php');
$router->instantiateRouteObjects($dic);
$destination = $router->getDestination();

$frontController = $dic->getInstance(new ObjectReference('Carrot\Core\FrontController{Main:Transient}'));
$response = $frontController->dispatch($dic, $destination);
$response->send();

exit;

/**
 * Instantiate the FrontController and dispatches the request. The
 * FrontController first uses RouterInterface to get the
 * destination instance and, after instantiating it with the DIC,
 * runs the appropriate method with it's arguments.
 */

$frontController = $dic->getInstance(new Carrot\Core\ObjectReference('Carrot\Core\FrontController{Main:Transient}'));
$response = $frontController->dispatch($dic);
$response->send();