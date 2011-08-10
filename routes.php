<?php

/**
 * Carrot route registration file.
 *
 * Register your route by accessing Carrot\Core\RouteRegistrations
 * instance through $routes variable.
 *
 * <code>
 * $routes->registerRouteObjectReference(
 *     'App.Login',
 *     new ObjectReference('App\Route\LoginRoute{Main:Transient}')
 * );
 * </code>
 * 
 * @see Carrot\Core\RouteRegistrations
 * 
 */

use Carrot\Core\ObjectReference;

$routes->registerBasicRoute('Carrot.Sample', array(
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