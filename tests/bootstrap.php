<?php

/**
 * Carrot PHPUnit bootstrap file.
 * 
 * Loads the autoloader configuration and registers it.
 *
 */

$autoloader = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'autoloader.php';
$autoloader->register();