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

$autoloader->bindNamespaceToDirectory('\\', __DIR__ . DIRECTORY_SEPARATOR . 'vendors');
$autoloader->bindNamespaceToDirectory('HelloWorld', __DIR__ . DIRECTORY_SEPARATOR . 'HelloWorld');