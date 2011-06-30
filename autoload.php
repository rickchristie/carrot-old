<?php

/**
 * Carrot autoloader file.
 *
 * This file is loaded by Carrot and has the responsibility of
 * defining the autoloader. Carrot doesn't care about the contents
 * of this file as long as every class file is autoloaded without
 * problems. The reason the autoloading process is on this file
 * instead of a Bootstrap class is to make it easier for you to
 * unit test your application.
 * 
 * See the documentation for Carrot\Core\Autoloader for more
 * information on how to properly autoload.
 *
 */

// Load the autoloader and namespace extractor class file
require __DIR__ . DIRECTORY_SEPARATOR . 'vendors' . DIRECTORY_SEPARATOR . 'Carrot' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Autoloader.php';

// Start the autoloader
$autoloader = new Carrot\Core\Autoloader(new ClassNameExtractor);
$autoloader->bindNamespaceToDirectory('\\', __DIR__ . DIRECTORY_SEPARATOR . 'vendors');
$autoloader->bindNamespaceToDirectory('\\', __DIR__ . DIRECTORY_SEPARATOR . 'providers');
$autoloader->register();