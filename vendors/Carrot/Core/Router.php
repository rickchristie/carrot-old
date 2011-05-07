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
 * Carrot's default Router. Uses two anonymous functions to store a two way route.
 * One anonymous function is responsible for routing, the other one is responsible
 * for reverse-routing. This class will call the routing functions one by one until
 * one of them returns an instance of Destination.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class Router implements \Carrot\Core\Interfaces\RouterInterface
{
    /**
     * @var array List of anonymous functions returning an instance of Destination.
     */
    protected $route_functions = array();
    
    /**
     * @var array List of anonymous functions returning a formatted URL.
     */
    protected $reverse_route_functions = array();
    
    /**
     * @var array List of route names that has been successfully defined.
     */
    protected $route_names = array();
    
    /**
     * @var Destination Default Destination object to return if there's no matching route. 
     */
    protected $no_matching_route_destination;
    
    /**
     * @var StdClass PHP standard class containing the routing parameters, to be passed to the routing/reverse-routing functions.
     */
    protected $params;
    
    /**
     * Constructs a Router object.
     *
     * Routing parameters set here in construction will be passed to the routing and reverse
     * routing function. They can be objects, strings, or plain arrays. Routin parameters is 
     * injected as arrays, but it will be casted into an object for easier access by the
     * anonymous functions, so make sure the index is simple strings. This array:
     *
     * <code>
     * $params = array('base_url' => 'http://localhost/carrot-dev', 'request' => $request);
     * </code>
     *
     * will be accessed as object at routing/reverse-routing functions:
     *
     * <code>
     * $params->base_url
     * $params->request
     * </code>
     *
     * @param array $params Routing parameters to be passed to the anonymous functions.
     * @param Destination $no_matching_route_destination Default destination to return when there is no matching route.
     * @param string $routes_file_path Absolute path to the file that contains the routes.
     *
     */
    public function __construct(array $params, \Carrot\Core\Destination $no_matching_route_destination, $routes_file_path)
    {
        $this->params = (object)$params;
        $this->no_matching_route_destination = $no_matching_route_destination;
        $this->loadRoutesFile($routes_file_path);
    }
    
    /**
     * Adds a new named route.
     * 
     * Your route must be defined in anonymous function that takes one argument: $params, which
     * contains the routing parameters. Routing parameters are set during object construction.
     * In addition to defining a routing function, you must also define a reverse-routing function,
     * this allows you to use two way routing.
     *
     * Routing function should use the routing parameters to determine if the current request
     * is its responsibility to route. On routing, this class will accept the first Destination
     * return as a valid route, so early defined routes always wins. Reverse routing function should
     * accept two parameters, $params (routing parameters), and additional $vars that contains arguments
     * that are sent via generateURL() method (see documentation for that method for more info).
     *
     * Here is an example of adding a route for home page (/), assuming that Request object
     * is injected as a parameter:
     * 
     * <code>
     * // Route:welcome
     * // Translates {/} to WelcomeController::index()
     * $router->addRoute
     * (   
     *     'welcome',
     *     function($params)
     *     {
     *         // Assuming that Request object is injected as a parameter at Router construction
     *         $uri_segments = $params->request->getAppRequestURISegments();
     *         
     *         // We don't need to return any value at all if it's not our route.
     *         if (empty($uri_segments))
     *         {
     *             return new Destination('\Carrot\Core\Controllers\WelcomeController@main', 'index');
     *         }
     *     },
     *     function($params, $vars)
     *     {
     *         // Since it's a route to the home page, we simply return a relative path.
     *         return $params->request->getBasePath();
     *     }
     * );
     * <code>
     *
     * Your reverse-routing function should return string, it will be casted to string anyway
     * by the generateURL() method.
     * 
     * @param string $route_name Name of the route, must be unique for each route.
     * @param Closure $route_function Anonymous function that returns an instance of Destination.
     * @param Closure $reverse_route_function Anonymous function that returns a URL string.
     *
     */
    public function addRoute($route_name, \Closure $route_function, \Closure $reverse_route_function)
    {
        if (in_array($route_name, $this->route_names))
        {
            throw new \RuntimeException("Router error in adding route, route name '{$route_name}' already defined.");
        }
        
        $this->route_names[] = $route_name;
        $this->route_functions[$route_name] = $route_function;
        $this->reverse_route_functions[$route_name] = $reverse_route_function;
    }
    
    /**
     * Walks through the defined route functions until one of them returns an instance of Destination.
     *
     * If none of the route returns an instance of Destination, this method will return no-matching-route
     * Destination instead. No matching route destination is set during object construction.
     * 
     * @return Destination
     *
     */
    public function getDestination()
    {
        $destination = $this->no_matching_route_destination;
        
        foreach ($this->route_functions as $route)
        {
            $return = $route($this->params);
            
            if (is_object($return) && is_a($return, '\Carrot\Core\Destination'))
            {
                $destination = $return;
                break;
            }
        }
        
        return $destination;
    }
    
    /**
     * Uses the user defined reverse-routing function to return a URL.
     * 
     * You can invoke the reverse-routing function you previously defined in your route using
     * this wrapper function. State the unique route name and variables to be passed to the
     * said function. The ideal way to use this method will be to inject the Router instance
     * to your template class so you can call it from your template files.
     *
     * If the provided route name does not exist, this method will throw a RuntimeException.
     * 
     * <code>
     * <a href="<?php $router->generateURL('blog_post', array('page' => 4), true) ?>"></a>
     * </code>
     * 
     * @param string $route_name Name of the route to invoke.
     * @param array $array_parameters Array parameters to send to reverse routing function.
     * @param bool $escape_url If true, the method escapes the returned string with htmlspecialchars(ENT_QUOTES).
     * @return string
     *
     */
    public function generateURL($route_name, array $array_parameters = array(), $escape_url = false)
    {
        if (!in_array($route_name, $this->route_names))
        {
            throw new \RuntimeException("Router error in generating URL, route name '{$route_name}' does not exist.");
        }
        
        $return = (string) $this->reverse_route_functions[$route_name]($this->params, $array_parameters);
        
        if ($escape_url)
        {
            #TODO: Find out if we have to define UTF-8 here
            $return = htmlspecialchars($return, ENT_QUOTES);
        }
        
        return $return;
    }
    
    /**
     * Sets a default destination to return if there is no matching route.
     *
     * @param Destination
     *
     */
    public function setDestinationForNoMatchingRoute(\Carrot\Core\Destination $destination)
    {
        $this->no_matching_route_destination = $destination;
    }
    
    /**
     * Gets the default destination to go to if there's no matching route.
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
            $require = function($router, $path)
            {
                require_once($path);
            };
            
            $require($this, $path);
        }
    }
}