<?php

/**
 * Carrot route registration file.
 *
 * Register your route classes here. Access Carrot's Router via
 * $router variable. Your route classes are registered by their
 * object reference (instance name) since the Router gets your
 * routes via DIC. This means you can tell the DIC to wire
 * dependencies for your route class.
 *
 * <code>
 * $router->registerRoute('routeID', new ObjectReference(
 *     'Namespace\RouteClassName'
 * ));
 * </code>
 * 
 * @see Carrot\Core\Router::registerRoute()
 * @see Carrot\Core\Router::loadConfigurationFile()
 * 
 */

use Carrot\Core\ObjectReference;

$router->registerRoute('Sample', new ObjectReference('Sample\Route{Main:Transient}'));
$router->registerRoute('CarrotDocs', new ObjectReference('Carrot\Docs\Route{Main:Transient}'));