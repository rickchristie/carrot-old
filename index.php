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

/**
 * Carrot advocates strict coding, thus this framework will report
 * all errors, even the tiniest mistakes. To control how the
 * errors are displayed, configure Carrot's ExceptionHandler.
 *
 */

error_reporting(E_ALL | E_STRICT);

/**
 * Geting the autoloader up and running. Loads user's autoload.php
 * file and registers the autoloader for you.
 *
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'Carrot' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Autoloader.php';
$autoloader = new Carrot\Core\Autoloader();
require __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';
$autoloader->register();

/**
 * Instantiates Dependency Injection Container, the core part of
 * Carrot that allows wiring of dependencies. Also registers the
 * default bindings for dependency injection container.
 *
 */

$dic = new Carrot\Core\DependencyInjectionContainer;

$dic->bind('Carrot\Core\AppRequestURI{Main:Transient}', array(
    $_SERVER['SCRIPT_NAME'],
    $_SERVER['REQUEST_URI']
));

$dic->bind('Carrot\Core\ExceptionHandler{Main:Transient}', array(
    $_SERVER['SERVER_PROTOCOL']
));

$dic->bind('Carrot\Core\Request{Main:Transient}', array(
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
 * Loads the user configuration file for Carrot's DIC. This is
 * where the user can override previously set default bindings.
 *
 */

$dic->loadConfigurationFile(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');

/**
 * Change error messages into ErrorException, and set the
 * exception handler. You can change the templates loaded when an
 * exception occurs by configuring Carrot's ExceptionHandler.
 *
 */

$errorHandler = function($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
};

set_error_handler($errorHandler);
$exceptionHandler = $dic->getInstance(new Carrot\Core\ObjectReference('Carrot\Core\ExceptionHandler{Main:Transient}'));
$exceptionHandler->set();

/**
 * Instantiates Carrot's Router and loads user's routes.php file.
 * Instantiates the route objects and translates the current
 * request into an instance of Destination.
 *
 */

$router = $dic->getInstance(new ObjectReference('Carrot\Core\Router{Main:Singleton}'));
$router->loadConfigurationFile(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php');
$router->instantiateRouteObjects($dic);
$destination = $router->getDestination();

/**
 * Dispatches the request by instantiating the routine object and
 * running the routine method. The resulting Response object is
 * then sent to the client.
 *
 */

$frontController = $dic->getInstance(new ObjectReference('Carrot\Core\FrontController{Main:Transient}'));
$response = $frontController->dispatch($dic, $destination);
$response->send();