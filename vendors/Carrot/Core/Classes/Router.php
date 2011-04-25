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
 * Router
 * 
 * Carrot's default Router. Uses chain of responsibility to store functions that
 * try to determine the destination. You can think of each anonymous function as
 * a Closure object representing a route.
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class Router implements \Carrot\Core\Interfaces\RouterInterface
{
	/**
	 * @var array List of anonymous functions as chain of responsibility.
	 */
	protected $chains = array();
	
	/**
	 * @var int The function index currently active.
	 */
	protected $active_index = -1;
	
	/**
	 * @var mixed Preferably a Request object.
	 */
	protected $request;
	
	/**
	 * @var mixed Preferably a Session object.
	 */
	protected $session;
	
	/**
	 * @var Destination Default destination if no matching route. 
	 */
	protected $no_matching_route_destination;
	
	/**
	 * @var Destination Default destination to go to if there is no route defined.
	 */
	protected $no_route_default_destination;
	
	/**
	 * Constructs a Router object.
	 *
	 * @param mixed $request Preferably a Request object.
	 * @param mixed $session Preferably a Session object.
	 *
	 */
	public function __construct($request, $session)
	{
		$this->request = $request;
		$this->session = $session;
		$this->no_matching_route_destination = new Destination('\Carrot\Core\Classes\SampleController:main', 'pageNotFound');
		$this->no_route_default_destination = new Destination('\Carrot\Core\Classes\SampleController:main', 'welcome');
	}
	
	/**
	 * Add a new function to the chain of responsibility.
	 *
	 * Your anonymous function should accept three parameters, $request,
	 * $session and the $router instance and return a Destination instance.
	 * If your anonymous function can't determine the route, pass the
	 * request and session to the next function in the chain of responsibility.
	 *
	 * <code>
	 * $router->add(function($request, $session, $router)
	 * {
	 *     // Returns a destination for '/'
	 *     if (empty($request->getAppRequestURISegments()))
	 *     {
	 *         return new Destination
	 *         (
	 *             '\Vendor\Namespace\HomeController:main',
	 *             'index',
	 *             array('Key Lime Pie', 'Cupcake', 'Orange Juice')
	 *         );
	 *     }
	 *
	 *     // Otherwise, not my responsibility
	 *     return $router->next($request, $session);
	 * });
	 * </code>
	 *
	 * Once you returned a destination, the chain stops, so if there are
	 * two functions handling the same route, the earliest function
	 * always wins.
	 *
	 * <<< WARNING >>> Don't call router methods other than next(), rewind()
	 * and getDestinationForNoMatchingRoute() inside the anonymous function,
	 * unless you wanted an unpredicted behavior (and possibly infinite loop).
	 *
	 * @param Closure $chain
	 *
	 */
	public function add(\Closure $chain)
	{
		$this->chains[] = $chain;
	}
	
	/**
	 * Starts the chain of responsibility to get the Destination object.
	 *
	 * If the returned value is not an instance of Destination, this method
	 * will return the no matching route destination instead.
	 *
	 * @return Destination
	 *
	 */
	public function getDestination()
	{
		if (empty($this->chains))
		{
			return $this->no_route_default_destination;
		}
		
		$this->active_index = -1;
		$destination = $this->next($this->request, $this->session);
		
		if (!is_a($destination, '\Carrot\Core\Classes\Destination'))
		{
			$destination = $this->no_matching_route_destination;
		}
		
		return $destination;
	}
	
	/**
	 * Calls the next function in the chain of responsibility.
	 * 
	 * Passes $request and $session to the next function. If no function is
	 * present, it will return the no matching route destination object.
	 *
	 * @param mixed $request Preferably the Request object.
	 * @param mixed $session Preferably the Session object.
	 *
	 */
	public function next($request, $session)
	{
		++$this->active_index;
		
		if (!isset($this->chains[$this->active_index]) or !is_callable($this->chains[$this->active_index]))
		{
			return $this->no_matching_route_destination;
		}
		
		return $this->chains[$this->active_index]($request, $session, $this);
	}
	
	/**
	 * Restarts the chain of responsibility with new parameters.
	 *
	 * It's usually a bad practice to redefine the user's request/session
	 * object and restart the chain of responsibility
	 *
	 * @param mixed $request Preferably a Request object.
	 * @param mixed $session Preferably a Session object.
	 *
	 */
	public function rewind($request, $session)
	{
		$this->active_index = -1;
		return $this->next($request, $session);
	}
	
	/**
	 * Sets a default destination to return to if there is no matching route.
	 *
	 * @param Destination
	 *
	 */
	public function setDestinationForNoMatchingRoute(\Carrot\Core\Classes\Destination $destination)
	{
		$this->no_matching_route_destination = $destination;
	}
	
	/**
	 * Gets the default destination to go if there's no matching route.
	 *
	 * @return Destination
	 *
	 */
	public function getDestinationForNoMatchingRoute()
	{	
		return $this->no_matching_route_destination;
	}
	
	/**
	 * Loads a file that defines routes.
	 *
	 * @param string $path Absolute path to the file.
	 *
	 */
	public function loadRoutesFile($path)
	{
		if (file_exists($path))
		{
			$router = $this;
			require($path);
		}
	}
}