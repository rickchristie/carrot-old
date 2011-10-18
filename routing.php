<?php

/**
 * Carrot routing configuration file.
 *
 * Each route in Carrot's Router is an object, specifically an
 * object that implements
 * Carrot\Core\Routing\Route\RouteInterface. You can implement
 * this interface to create your own custom route class. For most
 * cases, you can use generic route classes that are provided by
 * Carrot.
 *
 * In this file you can configure routing using the
 * implementation of Carrot\Core\Routing\Config\ConfigInterface
 * you've defined in the configuration file. You can access the
 * instance via $config variable.
 *
 */

use Carrot\Core\DependencyInjection\Reference,
    Carrot\Core\Routing\Destination;

$config->setNoMatchingHTTPRouteDestination(new Destination(
    new Reference('Carrot\Core\DefaultPages'),
    'HTTPNoMatchingRoute'
));

$config->setNoMatchingCLIRouteDestination(new Destination(
    new Reference('Carrot\Core\DefaultPages'),
    'CLINoMatchingRoute'
));

$config->addBasicHTTPRoute('Carrot:Sample', array(
    'pattern' => '/',
    'reference' => new Reference('Sample\Welcome'),
    'method' => 'getWelcomeResponse'
));