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

$autoloader->bindNamespaceToDirectory('Carrot', __DIR__ . DIRECTORY_SEPARATOR . 'Carrot');

/**
// ---------------------------------------------------------------
 * Below this comment block are the autoloading behavior needed
 * to set the provide
 *
 */

// Bind Carrot\SimpleDocs to the guide provider directory, since we are going to override the provider there.
$autoloader->bindNamespaceToDirectory('Carrot\SimpleDocs', __DIR__ . DIRECTORY_SEPARATOR . 'Sample' . DIRECTORY_SEPARATOR . 'Providers');
$autoloader->bindNamespaceToDirectory('Sample', __DIR__ . DIRECTORY_SEPARATOR . 'Sample');