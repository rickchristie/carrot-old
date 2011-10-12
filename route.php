<?php

/**
 * Carrot route configuration file.
 *
 * Each route in Carrot's Router is an object, specifically an
 * object that implements
 * Carrot\Core\Routing\Route\RouteInterface. You can implement
 * this interface to create your own custom route class. For most
 * cases, you can use generic route classes that are provided by
 * Carrot.
 *
//--------------------------------------------------------------- 
 * In this file you can configure routing 
 * 
 * Carrot's Router accepts each route as an object, specifically
 * an object that implements Carrot\Core\Routing\RouteInterface.
 * However, you don't need to create your own route class, since
 * Carrot provides generic route classes that you can use.
 * 
 * Carrot\Core\Routing\RouteRegistrator object helps you use
 * these generic routes without actually instantiating them
 * yourself. You can also create a custom route class and pass
 * in Carrot\Core\DependencyInjection\Reference instance to the
 * registrator, the route registrator object will use the
 * container to instantiate your route object.
 *
 */

$registrator->registerBasicRoute('Carrot.Sample', array(
    'pattern' => '/',
    'object' => 'Sample\Welcome{Main:Transient}',
    'method' => 'getWelcomeResponse'
));

$routes->registerBasicRoute('Carrot.Docs.Home', array(
    'pattern' => '/guides/',
    'object' => 'Carrot\Docs\Controller{Main:Transient}',
    'method' => 'getResponse'
));

$routes->registerBasicRoute('Carrot.Docs.Page', array(
    'pattern' => '/guides/{topicID}/{pageID}/',
    'object' => 'Carrot\Docs\Controller{Main:Transient}',
    'method' => 'getResponse',
    'args' => array('@topicID', '@pageID')
));

/**
 * Return the registrator object back to the caller.
 *
 * Carrot\Core\System expects this file to return the
 * $registrator variable.
 *
 */

return $registrator;