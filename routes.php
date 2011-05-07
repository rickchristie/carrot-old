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
 * Carrot's default Router's routes file.
 *
 * Carrot's default Router is configured to load this routes file in the default
 * dependency registration file for \Carrot\Core. You can use this file to define
 * routes for your application.
 *
 * You can set no-matching-route destination by editing the dependency registration
 * file for \Carrot\Core. You can also set it dynamically by adding this snippet
 * (adjust as necessary):
 *
 * <code>
 * $router->setDestinationForNoMatchingRoute(new Destination
 * (
 *     '\Namespace\Subnamespace\Controller@config_name',
 *     'method_name',
 *     array('args1', 'args2', ...);
 * );
 * </code>
 * 
 * Carrot's default Router handles two way routing by defining two anonymous functions,
 * one for routing and one to do reverse-routing. Routing function should accept
 * '$params' as a single parameter, an object that contains the routing parameters.
 * Routing function should return an instance of Destination if it thinks it can
 * route the current request. The earliest defined route always wins.
 *
 * Reverse-routing function, on the other hand, accepts $param (routing parameters)
 * and $vars - which is an array of additional arguments sent when Router::generateURL()
 * is invoked.
 *
 * <code>
 * // Route:blog_post
 * // Translates {/blog/$id} to \MyApp\BlogController::viewPost($id)
 * $router->addRoute
 * (
 *     'blog_post',
 *     function($params)
 *     {
 *         if (isset($params->uri_segments[0]) && $params->uri_segments[0] == 'blog')
 *         {
 *             // Specify a default value for $id
 *             $id = 0;
 *              
 *             // Use given value if they exists
 *             if (isset($params->uri_segments[1]))
 *             {
 *                 $id = (int) $params->uri_segments[1];
 *             }
 *             
 *             // Return the Destination
 *             return new Destination('\MyApp\BlogController@main', 'viewPost', array($id));
 *         }
 *     },
 *     function($params, $vars)
 *     {
 *         // Return relative path
 *         if (isset($vars['id']))
 *         {
 *             return $params->getBasePath() . $id;
 *         }
 *     }
 * );
 * </code>
 * 
 * Routing parameters are set during Router object construction. You can change
 * which object gets passed as routing parameters by editing the appropriate
 * dependency registration file.
 *
 */

use \Carrot\Core\Destination;

// Route:welcome
// Translates {/} to WelcomeController::index()
$router->addRoute
(   
    'welcome',
    function($params)
    {   
        // We don't need to return any value at all if it's not our route.
        if (empty($params->uri_segments))
        {
            return new Destination('\Carrot\Core\Controllers\WelcomeController@main', 'index');
        }
    },
    function($params, $vars)
    {
        // Since it's a route to the home page, we simply return a relative path.
        return $params->request->getBasePath();
    }
);