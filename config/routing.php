<?php

/**
 * Carrot routing configuration file.
 *
 * Each route in Carrot's Router is an object, specifically an
 * object that implements
 * Carrot\Routing\Route\RouteInterface. You can implement
 * this interface to create your own custom route class. For most
 * cases, you can use generic route classes that are provided by
 * Carrot.
 *
 * In this file you can configure routing using the
 * implementation of \Routing\Config\ConfigInterface
 * you've defined in the configuration file. You can access the
 * instance via $config variable.
 *
 */

use Carrot\DependencyInjection\Reference,
    Carrot\Routing\Destination;

$config->setNoMatchingHTTPRouteDestination(new Destination(
    new Reference('Carrot\Core\DefaultPages'),
    'HTTPNoMatchingRoute'
));

$config->setNoMatchingCLIRouteDestination(new Destination(
    new Reference('Carrot\Core\DefaultPages'),
    'CLINoMatchingRoute'
));

$config->addHTTPRoute(
    'Carrot:Docs',
    new Carrot\Docs\HTTPRoute()
);

$config->addBasicHTTPRoute('Carrot:Sample', array(
    'pattern' => '/',
    'reference' => new Reference('Sample\Welcome'),
    'method' => 'getWelcomeResponse'
));