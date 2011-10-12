<?php

/**
 * Carrot main configuration file.
 *
 * Used only for configuration values that are needed before the
 * dependency injection container is ready. You will have to
 * return the configuration values as an array.
 *
 */

/**
 * Path configuration.
 * 
 * Only index.php and config.php needs to stay at the web
 * server's root. You can move all other files by editing these
 * lines. Make sure to use only ABSOLUTE path. Make use of
 * __DIR__, DIRECTORY_SEPARATOR, DIRNAME() to create a more
 * platform agnostic format.
 *
 * You will have to define absolute paths to events, routes,
 * autoload and system file. Sample configuration:
 *
 * <code>
 * $config['paths']['events'] = '/absolute/path/to/file.php';
 * $config['paths']['routes'] = '/absolute/path/to/file.php';
 * $config['paths']['autoloader'] = '/absolute/path/to/file.php';
 * $config['paths']['system'] = '/absolute/path/to/file.php';
 * </code>
 *
 */

$config['files']['autoloader'] = __DIR__ . DIRECTORY_SEPARATOR . 'autoloader.php';
$config['files']['injectionConfig'] = __DIR__ . DIRECTORY_SEPARATOR . 'injection.php';
$config['files']['eventConfig'] = __DIR__ . DIRECTORY_SEPARATOR . 'event.php';
$config['files']['routingConfig'] = __DIR__ . DIRECTORY_SEPARATOR . 'route.php';
$config['files']['application'] = __DIR__ . DIRECTORY_SEPARATOR . 'Carrot' .
                            DIRECTORY_SEPARATOR . 'Core' .
                            DIRECTORY_SEPARATOR . 'Application.php';

/**
 * Configure which Carrot\Core\Request\RequestInterface
 * implementation is used in this application.
 * 
 * The request object represents the request environment and
 * becomes the main source to get access to PHP superglobals in
 * Carrot without violating scope. Used heavily in Carrot's
 * routing package.
 *
 * The implementation you choose will have a singleton lifecycle,
 * and can be accessed via the container using an unnamed
 * reference to Carrot\Core\Request\RequestInterface.
 * 
 */

$config['defaults']['request'] = array(
    'class' => 'Carrot\Core\Request\DefaultRequest',
    'name' => ''
);

/**
 * Configure which Carrot\Core\Logbook\LogbookInterface
 * implementation is used in this application.
 * 
 * The logbook is used for internal logging by Carrot's core
 * classes, including (not limited to) the dependency injection
 * container, event dispatcher, and router.
 *
 * The implementation you chose will have a singleton lifecycle,
 * and can be accessed via the container using an unnamed
 * reference to Carrot\Core\Logbook\LogbookInterface.
 *
 */

$config['defaults']['logbook'] = array(
    'class' => 'Carrot\Core\Logbook\Logbook',
    'name' => ''
);

/**
 * Configure which Carrot\Core\ExceptionHandler\HandlerInterface
 * implementation is used in this application.
 *
 * The implementation you chose will have a singleton lifecycle,
 * and can be accessed via the container using an unnamed
 * reference to Carrot\Core\ExceptionHandler\HandlerInterface.
 *
 */

$config['defaults']['exceptionHandler'] = array(
    'class' => 'Carrot\Core\ExceptionHandler\DebugHandler',
    'name' => ''
);

/**
 * Configure which Carrot\Core\Event\Dispatcher implementation is
 * used in this application.
 * 
 * The event dispatcher provides a simple way to hook your code
 * to Carrot's core processes.
 * 
 * The implementation you chose will have a singleton lifecycle,
 * and can be accessed via the container using an unnamed
 * reference to Carrot\Core\Event\Dispatcher.
 *
 */

$config['defaults']['eventDispatcher'] = array(
    'class' => 'Carrot\Core\Event\Dispatcher',
    'name' => ''
);

/**
 * Configure which Carrot\Core\Routing\Config\ConfigInterface
 * implementation is used in this application.
 * 
 * The routing configuration is used to translate user's
 * configuration into Route instances and setting main routing
 * parameters like the base URI scheme, authority, and path.
 * 
 * The implementation you chose will have a singleton lifecycle,
 * and can be accessed via the container using an unnamed
 * reference to Carrot\Core\Routing\Config\ConfigInterface.
 *
 */

$config['defaults']['routingConfig'] = array(
    'class' => 'Carrot\Core\Routing\Config\BasicConfig',
    'name' => ''
);

/**
 * Configure the base URL of this application.
 *
 * The base URL will be used in two-way routing HTTP request. The
 * base URL string will be parsed using parse_url(). If the base
 * URL if set to an empty string, Carrot\Core\Routing\Router will
 * try to guess it.
 *
 */

$config['baseURL'] = '';

/**
 * Return the configuration array to the caller.
 *
 * Carrot\Core\System expects this file to return a valid
 * configuration array.
 *
 */

return $config;