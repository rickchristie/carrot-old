<?php

namespace Carrot\Framework;

/**
 * Should you decide to move other files around, here are
 * variables that you must edit - they are paths to the
 * autoloader and configuration file, respectively.
 *
 */

$autoloaderFile = __DIR__ . DIRECTORY_SEPARATOR . 'autoloader.php';
$configFile = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

/**
 * Transform regular errors into exceptions.
 * 
 * Carrot requires all errors to be transformed as exceptions,
 * hence the need for this function to exist.
 * 
 * @see http://php.net/manual/en/class.errorexception.php
 *
 */
function exceptionErrorHandler($errno, $errstr, $errfile, $errline)
{
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
}

// Run the application.
set_error_handler('Carrot\Framework\exceptionErrorHandler');
$autoloader = require $autoloaderFile;
$config = new Config;
require $configFile;
$application = new Application($config, $autoloader);
$application->run();