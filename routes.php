<?php

/**
 * Carrot route registration file.
 *
 * This file is called by the default Router provider so that you
 * can register your routes without editing the provider files.
 * In this file you can access the router object using $router.
 *
 * To register a route:
 *
 * <code>
 * $router->registerRoute('routeID', 'Namespace\RouteClassName');
 * </code>
 * 
 * @see Carrot\Core\Router::registerRoute()
 * @see Carrot\Core\Router::loadRouteRegistrationFile()
 * 
 */

$router->registerRoute('Sample', 'Sample\Route');