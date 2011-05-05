<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>
 *
 * Licensed under the MIT License.
 *
 */

/**
 * The front controller will call RouterInterface::loadRoutesFile() with
 * the absolute path to this file as the sole argument. What the Router class
 * does with this file is entirely up to the author of the class.
 *
 * -- Information below only applies if you're using the default router --
 *
 * If you are using the default Router, you can use this file to define
 * routes and set no-matching-route destination. To set a default non-
 * matching-route destination, use:
 *
 * <code>
 * $router->setDestinationForNoMatchingRoute(new Destination
 * (
 *    '\Vendor\Namespace\Subnamespace\Controller@dicname',
 *    'method_name',
 *    array('Arguments')
 * );
 * </code>
 *
 * Carrot's default Router uses a simplified version of the chain of
 * responsibility pattern. For each route, we create an anonymous function
 * that either returns an instance of Destination or pass that responsibility
 * and arguments to the next function in the chain. Your anonymous function
 * must accept two arguments: $params and $router itself. Routing parameters
 * are set at object construction, so if you need to add a new routing parameter
 * (be it an object or simple string) edit the dependency registration file
 * for Carrot\Core.
 * 
 * Create a new chain by using Router::add(), like this:
 *
 * <code>
 * // Translates {/} to WelcomeController::index()
 * $router->add(function($params, $router)
 * {
 *      // Get app request uri in segments
 *      $app_request_uri = $params->request->getAppRequestURISegments();
 *      
 *      // Return destination if uri segment array is empty
 *      if (empty($app_request_uri))
 *      {
 *          return new Destination
 *          (
 *              '\Carrot\Core\Controllers\WelcomeController@main',
 *              'index'
 *          );
 *      }
 *
 *      // Otherwise, not my responsibility, pass arguments to the next chain
 *      return $router->next($params, $router);
 * });
 * </code>
 *
 * If the chain has been exhausted and we still don't have a Destination,
 * the Router will return no-matching-route destination to the front
 * controller.
 *
 */

use \Carrot\Core\Destination;

// Translates {/} to WelcomeController::index()
$router->add(function($params, $router)
{
    // Get app request uri in segments
    $app_request_uri = $params->request->getAppRequestURISegments();
    
    // Return destination if uri segment array is empty
    if (empty($app_request_uri))
    {
        return new Destination
        (
            '\Carrot\Core\Controllers\WelcomeController@main',
            'index'
        );
    }
    
    // Otherwise, not my responsibility, pass arguments to the next chain
    return $router->next($params, $router);
});

/*

$router->add('welcome', function($params)
{
    
}, function($params)
{
    
})

// Translates {/} to WelcomeController::index()
$router->addRoute
(
    'welcome',
    function($params)
    {
        if (empty($params->request->getAppRequestURISegments()))
        {
            return new Destination('\Carrot\Core\Controllers\WelcomeController@main', 'index');
        }
    },
    function($params)
    {
        return $params->request->getBaseURL();
    }
); */