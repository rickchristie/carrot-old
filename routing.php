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

use Carrot\Core\DependencyInjection\Reference;

$config->addBasicHTTPRoute('Carrot.Sample', array(
    'pattern' => '/',
    'reference' => new Reference('Sample\Welcome'),
    'method' => 'getWelcomeResponse'
));

$config->addBasicHTTPRoute('Carrot.Docs.Home', array(
    'pattern' => '/guides/',
    'reference' => new Reference('Carrot\Docs\Controller'),
    'method' => 'getResponse'
));

$config->addBasicHTTPRoute('Carrot.Docs.Page', array(
    'pattern' => '/möchter/<topicID>/<r:pageID:(%[A-Fa-f0-9]{2})>/<b:add>',
    'reference' => new Reference('Carrot\Docs\Controller'),
    'method' => 'getResponse',
    'args' => array('<topicID>', '<b:add>')
));

/**
 * Return the registrator object back to the caller.
 *
//---------------------------------------------------------------
 * Carrot\Core\Application expects this file to return the
 * $registrator variable.
 *
 */

return $config;