<?php

/**
 * Dependency injection configuration file.
 * 
 * Carrot depends on the dependency container to wire
 * dependencies of most of the objects needed to generate the
 * response, especially yours.
 *
 * You can read more about how to utilize injection configuration
 * objects and how to create your own injection configuration
 * implementation at the documentation.
 *
 * In this file, you must instantiate an implementation of
 * Carrot\Core\DependencyInjection\ConfigInterface, configure
 * it as needed, and return the instance to the caller.
 *
 */

use Carrot\Core\DependencyInjection\Reference,
    Carrot\Core\DependencyInjection\Config\BindingsConfig,
    Carrot\Core\DependencyInjection\Injector\ConstructorInjector;

/**
 * Use bindings dependency injection configuration object.
 * 
 * Bindings dependency injection configuration object uses
 * reflection to automatically generate constructor injectors on
 * demand. Since it uses reflection heavily, expect to trade
 * performance for convenience.
 *
 */

$config = new BindingsConfig;

/**
 * Write your configuration here.
 * 
//---------------------------------------------------------------
 * 
 * 
 * 
 * After the dependency injection container is properly
 * instantiated, Carrot uses the container to get core classes
 * that is needs. Specifically, Carrot will use the container to
 * get the following instances:
 *
 * <code>
 * Reference('Carrot\Core\Logbook\LogbookInterface')
 * Reference('Carrot\Core\ExceptionHandler\HandlerInterface')
 * Reference('Carrot\Core\Event\HandlerInterface')
 * Reference('Carrot\Core\Routing\Config\ConfigInterface')
 * </code>
 * 
 * The references listed above are reserved for Carrot's internal
 * core, which means if you change them, you will be changing
 * Carrot's behavior. They will be respectively used for internal
 * systems logging, exception handler, event handler, and routing
 * configuration.
 * 
 * Below are the default configurations. You may modify them as
 * you need. The config lines below should work regardless of the
 * injection configuration implementation you use.
 * 
 * Please see the docs on each of the classes for more
 * information about what needs to be injected, and the
 * responsibility of the said classes.
 *
 */

$config->addInjector(new ConstructorInjector(
    new Reference('Carrot\Core\Request\DefaultRequest'),
    array(
        $_SERVER,
        $_GET,
        $_POST,
        $_FILES,
        $_COOKIE,
        $_REQUEST,
        $_ENV
    )
));

$config->addInjector(new ConstructorInjector(
    new Reference('Carrot\Core\ExceptionHandler\DebugHandler'),
    array(
        new Reference('Carrot\Core\Logbook\LogbookInterface'),
        $_SERVER
    )
));

/**
 * Return the configuration object to the caller.
 * 
 * Carrot\Core\System expects this file to return an instance of
 * Carrot\Core\DependencyInjection\ConfigInterface.
 *
 */

return $config;