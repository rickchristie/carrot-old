<?php

namespace Carrot\Framework;

/**
 * Carrot configuration file. You can access the Config object
 * via the $config variable. 
 * 
 * If your application needs additional configuration values or
 * if you need additional methods, simply extend Carrot's Config
 * object, instantiate it here and return it to replace the
 * default Config object.
 * 
 * NOTE: You can also access the request object in this file, via
 * the $request variable.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */

/**
 * Set the default 404 page template file to be used. The
 * template is only used when Carrot is not in debug mode.
 *
 */
$config->set404TemplatePath(
    dirname(__DIR__) . DIRECTORY_SEPARATOR .
    'templates' . DIRECTORY_SEPARATOR .
    'errors' . DIRECTORY_SEPARATOR .
    '404.php'
);

/**
 * Set the default 500 page template file to be used. The
 * template is only used when Carrot is not in debug mode.
 *
 */
$config->set500TemplatePath(
    dirname(__DIR__) . DIRECTORY_SEPARATOR .
    'templates' . DIRECTORY_SEPARATOR .
    'errors' . DIRECTORY_SEPARATOR .
    '500.php'
);

/**
 * Set the routes file path. Routes file is used to store routes
 * configuration.
 *
 */
$config->setRoutesFilePath(
    __DIR__ . DIRECTORY_SEPARATOR .
    'routes.php'
);

/**
 * Set default timezone string to be used.
 *
 */
$config->setTimezone('Asia/Jakarta');

/**
 * Set debug mode. Set to TRUE to activate debug mode and display
 * error messages, FALSE otherwise.
 *
 */
$config->setDebugMode(TRUE);

/**
 * Set the admin email.
 *
 */
$config->setAdminEmail('seven.rchristie@gmail.com');

/**
 * Set to TRUE if you want the framework to send email on error
 * to the admin email.
 *
 */
$config->setSendEmailOnError(TRUE);

/**
 * Do not remove this line. The configuration object must be
 * returned to the App object.
 *
 */
return $config;