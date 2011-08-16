<?php

/**
 * Carrot autoloader file.
 *
 * Access Carrot's autoloader using $autoloader and configure it
 * so that it knows where to load your class files. You can bind
 * namespaces to a folder or bind a specific class to a specific
 * file.
 *
 * <code>
 * $autoloader->bindNamespaceToDirectory('Carrot', __DIR__ . DIRECTORY_SEPARATOR . 'Carrot);
 * </code>
 *
 * When you bind a namespace to a directory, the file names and
 * location must adhere to PSR-0 universal autoloader proposal.
 *
 * @see http://groups.google.com/group/php-standards/web/psr-0-final-proposal
 * @see Carrot\Core\Autoloader
 *
 */

$autoloader->bindNamespaceToDirectory('Carrot', __DIR__ . DIRECTORY_SEPARATOR . 'Carrot');
$autoloader->bindNamespaceToDirectory('Sample', __DIR__ . DIRECTORY_SEPARATOR . 'Sample');