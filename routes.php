<?php

/**
 * List of routes, loaded by Carrot's DefaultRouter class.
 *
 * <<< IF YOU ARE USING THE DEFAULT ROUTER >>>
 *
 * Create a new chain by using RouterChain::add(), like this:
 *
 * <code>
 * $router->add(function($request, $session, $router)
 * {
 *    if ($request->getAppRequestURISegments(0) == 'about')
 *    {
 *        return new Destination
 *        (
 *            'controller' => '\Vendor\Namespace\Subnamespace\Class:name',
 *            'method' => 'index',
 *            'params' => array('Key Lime Pie', 'Black Forest', 'Orange Juice');
 *        );
 *    }
 *    
 *    // We can't handle this route, pass the responsibility to the next chain
 *    return $router->next($request, $session, $router);
 * });
 * </code>
 *
 * <<< IF YOU ARE USING CUSTOM ROUTER >>>
 *
 * If you build the class yourself, you should know what whether this file
 * is needed or not and what to write here. If you are using somebody
 * else's router, read their documentation. Depending on the router class,
 * this file may not be loaded at all.
 *
 */

use \Carrot\Core\Classes\Destination;

// Translates {/} to SampleController::welcome()
$router->add(function($request, $session, $router)
{
	$app_request_uri = $request->getAppRequestURISegments();
	
	if (empty($app_request_uri))
	{
		return new Destination
		(
			'\Carrot\Core\Classes\SampleController@main',
			'welcome'
		);
	}
	
	return $router->next($request, $session, $router);
});