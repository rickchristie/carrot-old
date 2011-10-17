<?php

/**
 * Carrot autoloader file.
 *
 * This file is solely responsible of handling autoloading for
 * the entire application. After loading this file, Carrot will
 * register the autoloader object returned and assume that class
 * file autoloading for the entire application is handled.
 *
 * In this file, you must instantiate an implementation of
 * Carrot\Core\Autoloader\AutoloaderInterface, configure it as
 * needed, and return the instance to the caller.
 *
 */

require __DIR__ . DIRECTORY_SEPARATOR .
        'Carrot' . DIRECTORY_SEPARATOR .
        'Core' . DIRECTORY_SEPARATOR . 
        'Autoloader' . DIRECTORY_SEPARATOR .
        'AutoloaderInterface.php';

require __DIR__ . DIRECTORY_SEPARATOR .
        'Carrot' . DIRECTORY_SEPARATOR .
        'Core' . DIRECTORY_SEPARATOR . 
        'Autoloader' . DIRECTORY_SEPARATOR .
        'PSR0Autoloader.php';

$autoloader = new Carrot\Core\Autoloader\PSR0Autoloader;
$autoloader->bindNamespace('Carrot', __DIR__ . DIRECTORY_SEPARATOR . 'Carrot');
$autoloader->bindNamespace('Sample', __DIR__ . DIRECTORY_SEPARATOR . 'Sample');

/**
 * Return the autoloader object to the caller.
 *
 * Carrot\Core\Application expects this file to return an
 * instance of Carrot\Core\Autoloader\AutoloaderInterface.
 *
 */

return $autoloader;