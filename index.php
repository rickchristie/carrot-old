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

/**
 * Autoload behavior
 *
 * Implementation adheres to the PSR-0 universal autoloader final proposal. Modified
 * from a sample found in: {@link http://groups.google.com/group/php-standards/web/psr-0-final-proposal?pli=1}
 *
 * If you have a class that does not adhere to the PSR-0 universal autoloader final
 * proposal, simply add another function to the spl_autoload_register list. You can
 * add them in /autoload.php.
 *
 */

spl_autoload_register(function($class)
{
    $class = ltrim($class, '\\');
    $path = '';
    $namespace = '';
    
    // Separate namespace with class, change namespace to path
    if ($last_namespace_pos = strripos($class, '\\'))
    {
        $namespace = substr($class, 0, $last_namespace_pos);
        $class = substr($class, $last_namespace_pos + 1);
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'vendors' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    
    $path .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    
    if (file_exists($path))
    {
        require $path;
    }
});

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