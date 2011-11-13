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
 * Carrot\Autoloader\AutoloaderInterface, configure it as
 * needed, and return the instance to the caller.
 *
 */

require dirname(__DIR__) . DIRECTORY_SEPARATOR .
        'library' . DIRECTORY_SEPARATOR .
        'Carrot' . DIRECTORY_SEPARATOR .
        'Autoloader' . DIRECTORY_SEPARATOR .
        'AutoloaderInterface.php';

require dirname(__DIR__) . DIRECTORY_SEPARATOR .
        'library' . DIRECTORY_SEPARATOR .
        'Carrot' . DIRECTORY_SEPARATOR .
        'Autoloader' . DIRECTORY_SEPARATOR .
        'PSR0Autoloader.php';

$autoloader = new Carrot\Autoloader\PSR0Autoloader;
$autoloader->bindNamespace('\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'library');
$autoloader->bindNamespace('Sample', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Sample');

/**
 * Return the autoloader object to the caller.
 *
 * Carrot\Core\Application expects this file to return an
 * instance of Carrot\Autoloader\AutoloaderInterface.
 *
 */

return $autoloader;