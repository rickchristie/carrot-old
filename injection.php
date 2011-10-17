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
 * Write your dependency injection configuration here.
 * 
 * Please note that how you write your configuration may be
 * different depending on which configuration object you use.
 * However, the default configurations below should work
 * regardless of the injection configuration object you use.
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
 * Carrot\Core\Application expects this file to return an
 * instance of Carrot\Core\DependencyInjection\ConfigInterface.
 *
 */

return $config;