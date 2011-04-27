<?php

/**
 * The front controller will use RouterInterface::loadRoutesFile() with
 * an absolute path to this file. What the Router class does with this
 * file is entirely up to the author of the class.
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
 * must accept three arguments, $request, $session, and $router itself. 
 * 
 * Create a new chain by using Router::add(), like this:
 *
 * <code>
 * // Translates {/} to SampleController::welcome()
 * $router->add(function($request, $session, $router)
 * {
 *     // Get app request uri in segments
 *     $app_request_uri = $request->getAppRequestURISegments();
 *
 *     // Return destination if uri segment array is empty
 *     if (empty($app_request_uri))
 *     {
 *         return new Destination
 *         (
 *             '\Carrot\Core\Classes\SampleController@main',
 *             'welcome',
 *             array('Arguments')
 *         );
 *     }
 *  
 *     // Otherwise, not my responsibility, pass arguments to the next chain
 *     return $router->next($request, $session, $router);
 * });
 * </code>
 *
 * If the chain has been exhausted and we still don't have a Destination,
 * the Router will return no-matching-route destination to the front
 * controller.
 *
 */

use \Carrot\Core\Classes\Destination;

// Translates {/} to SampleController::welcome()
$router->add(function($request, $session, $router)
{
    // Get app request uri in segments
    $app_request_uri = $request->getAppRequestURISegments();
    
    // Return destination if uri segment array is empty
    if (empty($app_request_uri))
    {
        return new Destination
        (
            '\Carrot\Core\Classes\SampleController@main',
            'welcome'
        );
    }
    
    // Otherwise, not my responsibility, pass arguments to the next chain
    return $router->next($request, $session, $router);
});