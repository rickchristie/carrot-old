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
 * Carrot\DependencyInjection\ConfigInterface, configure
 * it as needed, and return the instance to the caller.
 *
 */

use Carrot\DependencyInjection\Reference,
    Carrot\DependencyInjection\Config\BindingsConfig,
    Carrot\DependencyInjection\Injector\ConstructorInjector,
    Carrot\DependencyInjection\Injector\FauxInjector;

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
    new Reference('Carrot\Request\DefaultRequest'),
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
    new Reference('Carrot\ExceptionHandler\DebugHandler'),
    array(
        new Reference('Carrot\Logbook\LogbookInterface'),
        new Reference('Carrot\Request\RequestInterface')
    )
));

$config->addInjector(new FauxInjector(
    new Reference('Carrot\Core\DefaultPages'),
    new Carrot\Core\DefaultPages
));

$config->addInjector(new ConstructorInjector(
    new Reference('Carrot\Docs\View'),
    array(
        new Carrot\Docs\Storage,
        new Reference('Carrot\Routing\RouterInterface'),
        'Carrot:Docs'
    )
));

/**
 * Return the configuration object to the caller.
 * 
 * Carrot\Core\Application expects this file to return an
 * instance of Carrot\DependencyInjection\ConfigInterface.
 *
 */

return $config;