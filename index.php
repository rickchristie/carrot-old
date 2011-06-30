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
 * Geting the autoloader up and running. Note that Carrot doesn't
 * care if you're using the default Autoloader class or not.
 * What's important is that after requiring this file, all classes
 * are autoloaded successfully.
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Instantiates the dependency injection container and load the
 * providers.php file. In that file you can map providers
 * according to your liking.
 */

$dic = new Carrot\Core\DependencyInjectionContainer;
$dic->loadProviderFile(__DIR__ . DIRECTORY_SEPARATOR . 'providers.php');

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
$exceptionHandler = $dic->getInstance('Carrot\Core\ExceptionHandler@Main');
$exceptionHandler->set();

/**
 * Instantiate the FrontController and dispatches the request. The
 * FrontController first uses RouterInterface to get the
 * destination instance and, after instantiating it with the DIC,
 * runs the appropriate method with it's arguments.
 */

$frontController = $dic->getInstance('Carrot\Core\FrontController@Main');
$response = $frontController->dispatch($dic);
$response->send();