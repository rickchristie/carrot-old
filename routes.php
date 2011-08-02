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

$routes->registerRouteObjectReference('Sample', new ObjectReference('Sample\Route{Main:Transient}'));
$routes->registerRouteObjectReference('CarrotDocs', new ObjectReference('Carrot\Docs\Route{Main:Transient}'));