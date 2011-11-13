<?php

/**
 * Carrot main configuration file.
 *
 * In this file, you can configure the location of other
 * configuration files, default implementations of interfaces
 * used by core classes, and other tidbits of Carrot.
 *
 */

/**
 * Path configuration.
 * 
 * You can move other configuration files by editing these lines.
 * Make sure to use only ABSOLUTE path. Always use __DIR__,
 * DIRECTORY_SEPARATOR, dirname() to create a more platform
 * agnostic format.
 *
 */

$config['files']['autoloader'] = __DIR__ . DIRECTORY_SEPARATOR . 'autoloader.php';
$config['files']['injectionConfig'] = __DIR__ . DIRECTORY_SEPARATOR . 'injection.php';
$config['files']['eventConfig'] = __DIR__ . DIRECTORY_SEPARATOR . 'event.php';
$config['files']['routingConfig'] = __DIR__ . DIRECTORY_SEPARATOR . 'routing.php';
$config['files']['application'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'library'
                                . DIRECTORY_SEPARATOR . 'Carrot'
                                . DIRECTORY_SEPARATOR . 'Core'
                                . DIRECTORY_SEPARATOR . 'Application.php';

/**
 * Configure which Carrot\Request\RequestInterface implementation
 * is used in this application.
 * 
 * The request object represents the request environment and
 * becomes the main source to get access to PHP superglobals in
 * Carrot without violating scope.
 *
 * The implementation you choose will have a singleton lifecycle,
 * and can be accessed via the DIC using an unnamed reference to
 * Carrot\Request\RequestInterface.
 * 
 */

$config['defaults']['request'] = array(
    'class' => 'Carrot\Request\DefaultRequest',
    'name' => ''
);

/**
 * Configure which Carrot\Logbook\LogbookInterface implementation
 * is used in this application.
 * 
 * The logbook is used for internal logging by Carrot's core
 * classes, including (not limited to) the dependency injection
 * container, event dispatcher, and router.
 *
 * The implementation you chose will have a singleton lifecycle,
 * and can be accessed via the DIC using an unnamed reference to
 * Carrot\Logbook\LogbookInterface.
 *
 */

$config['defaults']['logbook'] = array(
    'class' => 'Carrot\Logbook\DefaultLogbook',
    'name' => ''
);

/**
 * Configure which Carrot\ExceptionHandler\HandlerInterface
 * implementation is used in this application.
 *
 * The implementation you chose will have a singleton lifecycle,
 * and can be accessed via the DIC using an unnamed reference to
 * Carrot\ExceptionHandler\HandlerInterface.
 *
 */

$config['defaults']['exceptionHandler'] = array(
    'class' => 'Carrot\ExceptionHandler\DebugHandler',
    'name' => ''
);

/**
 * Configure which Carrot\Event\DispatcherInterface
 * implementation is used in this application.
 * 
 * The event dispatcher provides a simple way to hook your code
 * to Carrot's core processes.
 * 
 * The implementation you chose will have a singleton lifecycle,
 * and can be accessed via the DIC using an unnamed reference to
 * Carrot\Event\DispatcherInterface.
 *
 */

$config['defaults']['eventDispatcher'] = array(
    'class' => 'Carrot\Event\Dispatcher',
    'name' => ''
);

/**
 * Configure which Carrot\Routing\Config\ConfigInterface
 * implementation is used in this application.
 * 
 * The routing configuration is used to translate user's
 * configuration into RouteInterface implementations.
 * 
 * The implementation you chose will have a singleton lifecycle,
 * and can be accessed via the container using an unnamed
 * reference to Carrot\Routing\Config\ConfigInterface.
 *
 */

$config['defaults']['routingConfig'] = array(
    'class' => 'Carrot\Routing\Config\BasicConfig',
    'name' => ''
);

/**
 * Configure which Carrot\Routing\HTTPURIInterface implementation
 * to be used in this application.
 * 
 * Carrot's default implementation supports unicode, but only as
 * UTF-8. Helper methods from this object will not work correctly
 * if the encoding of the HTTP URI sent is not in UTF-8. If you
 * wanted the routing process to accept and route HTTP request
 * URI's not in UTF-8, you will have to define your own
 * implementation of various routing object, including this one. 
 *
 * Since Carrot\Routing\HTTPURIInterface is a value object,
 * you do not need to provide an instance name, only the fully
 * qualified class name is needed.
 *
 */

$config['defaults']['HTTPURI'] = 'Carrot\Routing\HTTPURI';

/**
 * Configure the base HTTP URI of this application.
 *
 * These information are used in creating a base HTTP URI object,
 * to be passed to the route objects in routing. If the the
 * scheme, authority or the path is empty, the Router class will
 * try to guess it.
 *
 * <code>
 * $config['base']['scheme'] = 'http';
 * $config['base']['authority'] = 'example.com';
 * $config['base']['path'] = '/';
 * </code>
 *
 */

$config['base']['scheme'] = '';
$config['base']['authority'] = '';
$config['base']['path'] = '';

/**
 * Return the configuration array to the caller.
 *
 * Carrot\Core\Application expects this file to return a valid
 * configuration array.
 *
 */

return $config;