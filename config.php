<?php

/**
 * Main configuration file.
 * 
 * You should be able to access the configuration object using
 * $config variable. Read the documentation on the configuration
 * class for the list of things you can do.
 *
 */

/**
 * Path configuration.
 * 
 * You can move other configuration files by editing these lines.
 * Use absolute path. Always use __DIR__, DIRECTORY_SEPARATOR,
 * and dirname() to create a platform agnostic path configuration.
 *
 */

$config->setInjectionsFile(
    __DIR__ . DIRECTORY_SEPARATOR .
    'config' . DIRECTORY_SEPARATOR .
    'injections.php'
);

$config->setRoutesFile(
    __DIR__ . DIRECTORY_SEPARATOR .
    'config' . DIRECTORY_SEPARATOR .
    'injections.php'
);

$config->setEventsFile(
    __DIR__ . DIRECTORY_SEPARATOR .
    'config' . DIRECTORY_SEPARATOR .
    'events.php'
);

/**
 * Custom classes.
 * 
 * You can modify Carrot's behavior by replacing the classes it
 * uses for specific purposes.
 *
 */

$config->setExceptionHandler(
    new Carrot\Injection\Reference(
        'Carrot\Framework\Error\ExceptionHandler'
    )
);