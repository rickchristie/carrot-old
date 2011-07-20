<?php

/**
 * Carrot autoloader file.
 *
 * In this file, you can access the Autoloader class using the
 * $autoloader variable. Use it to define your application's
 * autoloading behavior.
 * 
 * @see Carrot\Core\Autoloader
 *
 */





/**
 * blah
 *
 */

$autoloader->bindNamespaceToDirectory('Carrot', __DIR__ . DIRECTORY_SEPARATOR . 'Carrot');
$autoloader->bindNamespaceToDirectory('Sample', __DIR__ . DIRECTORY_SEPARATOR . 'Sample');