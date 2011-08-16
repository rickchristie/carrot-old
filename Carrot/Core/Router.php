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
 * Routing is the process of determining which routine object to
 * instantiate, which routine method to call, and what arguments
 * should be passed to the routine method. These informations are
 * represented by an instance of Callback.
 *
 * This class manages routes. Routes are classes that contains
 * the logic and data to translate the current request into an
 * instance of Callback. Route classes must implement
 * RouteInterface.
 *
 * The routing process is simple. When doRouting() is called, this
 * class will loop through all registered route classes and calls
 * RouteInterface::route() until one of them returns an
 * instance of Callback.
 *
 * TODO: Explain usage!
 * 
 * For more information on the route class please see the docs on
 * {@see RouteInterface}.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Carrot\Core\Interfaces\RouteInterface;
use InvalidArgumentException;
use RuntimeException;

class Router
{
    /**
     * @var RouteRegistrations The object representing registered routes.
     */
    protected $routeRegistrations;
    
    /**
     * @var AppRequestURI Used to get the base path and base URL value.
     */
    protected $appRequestURI;
    
    /**
     * @var array The list of registered route IDs.
     */
    protected $registeredRouteIDs;
    
    /**
     * @var array Contains the list of registered routes and its instantiated route objects.
     */
    protected $routes = array();
    
    /**
     * @var string The route ID of the registration that returned the callback.
     */
    protected $activeRouteID;
    
    /**
     * @var Callback The Callback instance returned after routing.
     */
    protected $activeCallback;
    
    /**
     * @var bool Set to TRUE if the doRouting() method is called at least once, FALSE otherwise.
     */
    protected $routingHasBeenDone;
    
    /**
     * Constructor.
     *
     * RouteRegistrations represents the route registrations and is
     * used by this class to retrieve the route objects. Pass an
     * instance of this object when constructing:
     *
     * <code>
     * $router = new Router($routeRegistrations);
     * </code>
     *
     * @param RouteRegistrations $routeRegistrations The RouteRegistration instance.
     * @param AppRequestURI $appRequestURI Used to get the base path and base URL value.
     *
     */
    public function __construct(RouteRegistrations $routeRegistrations, AppRequestURI $appRequestURI)
    {
        $this->appRequestURI = $appRequestURI;
        $this->routeRegistrations = $routeRegistrations;
        $this->registeredRouteIDs = $this->routeRegistrations->getRegisteredRouteIDs();
    }
    
    /**
     * Routes the request into a callback instance.
     * 
     * The actual routing method. Loops through the RouteRegistrations
     * objects and return immediately if one of them returns an
     * instance of Callback.
     *
     * Sets the $routingHasBeenDone class property just before
     * returning the Callback instance.
     * 
     * @return Callback The callback instance.
     *
     */
    public function doRouting()
    {   
        foreach ($this->registeredRouteIDs as $routeID)
        {
            if (!isset($this->routes[$routeID]))
            {
                $this->routes[$routeID] = $this->routeRegistrations->getRouteObject($routeID);
            }
            
            $callback = $this->routes[$routeID]->route();
            
            if ($this->validCallback($callback))
            {   
                $this->activeRouteID = $routeID;
                $this->activeCallback = $callback;
                $this->routingHasBeenDone = TRUE;
                return $callback;
            }
        }
        
        $callback = $this->getCallbackForNoMatchingRoute();
        $this->activeRouteID = FALSE;
        $this->activeCallback = $callback;
        $this->routingHasBeenDone = TRUE;
        return $callback;
    }
    
    /**
     * Gets the URL from the route object.
     * 
     * This method is to be utilized in your views or templates to do
     * two way routing.
     *
     * <code>
     * <a href="<?php echo $router->getURL('blogShow', array('id' => $id)) ?>">See more</a>
     * </code>
     * 
     * @param string $routeID The ID of the route.
     * @param array $args The arguments to send to the route.
     *
     */
    public function getURL($routeID, array $args = array(), $absoluteURL = false)
    {   
        if (!in_array($routeID, $this->registeredRouteIDs))
        {
            throw new InvalidArgumentException("Router error in getting URL. The route with ID '{$routeID}' is not registered.");
        }
        
        if (!isset($this->routes[$routeID]))
        {
            $this->routes[$routeID] = $this->routeRegistrations->getRouteObject($routeID);
        }
        
        $relativePath = $this->routes[$routeID]->getRelativePath($args);
        $relativePath = ltrim($relativePath, '/');
        
        if ($absoluteURL)
        {
            return $this->appRequestURI->getBaseURL() . $relativePath;
        }
        else
        {
            return $this->appRequestURI->getBasePath() . $relativePath;
        }
    }
    
    /**
     * Returns the active route ID.
     * 
     * Returns the ID of the route that is responsible for returning
     * the Callback instance after doRouting() is called.
     * 
     * Returns false if doRouting() has been called but there is no
     * matching route, which also means that the callback for no
     * matching route is the active one.
     *
     * Throws RuntimeException if this method is called before
     * doRouting() is called at least once.
     * 
     * @throws RuntimeException
     * @return mixed Either the active route ID, false, or null.
     *
     */
    public function getActiveRouteID()
    {
        if ($this->routingHasBeenDone == FALSE)
        {
            throw new RuntimeException("Router error in getting the active route ID. Routing hasn't been done yet.");
        }
        
        return $this->activeRouteID;
    }
    
    /**
     * Returns the active callback instance.
     *
     * Returns the callback instance returned by doRouting() method
     * when it is called by System. If there is no matching route
     * found when routing, this method will return the callback for
     * no matching route.
     * 
     * Returns null if doRouting() hasn't been called yet.
     *
     * @return Callback The callback instance returned by doRouting() method.
     *
     */
    public function getActiveCallback()
    {
        if ($this->routingHasBeenDone == FALSE)
        {
            throw new RuntimeException("Router error in getting the active callback. Routing hasn't been done yet.");
        }
        
        return $this->activeCallback;
    }
    
    /**
     * Return an array containing all the registered route IDs.
     *
     * Useful when you are building a sitemap or navigation object.
     * Example return:
     *
     * <code>
     * $routeIDs = array(
     *     'App.Login',
     *     'App.Blog.Show',
     *     'App.Blog.Comment'
     * );
     * </code>
     *
     * @return array All the registered route IDs.
     *
     */
    public function getAllRegisteredRouteIDs()
    {
        return $this->registeredRouteIDs;
    }
    
    /**
     * Returns the default callback instance for no matching route.
     *
     * Throws RuntimeException if the default callback instance for no
     * matching route is not set.
     *
     * @see doRouting()
     * @throws RuntimeException
     * @return Callback
     *
     */
    protected function getCallbackForNoMatchingRoute()
    {   
        $callback = $this->routeRegistrations->getCallbackForNoMatchingRoute();
        
        if ($this->validCallback($callback))
        {
            return $callback;
        }
        
        throw new RuntimeException("Router error in routing request. No matching route was found and there is no default callback instance set to be returned for no matching route.");
    }
    
    /**
     * Checks if the variable is an object and an instance of Callback.
     *
     * @param mixed $callback
     * @return bool
     *
     */
    protected function validCallback($callback)
    {
        return (is_object($callback) && $callback instanceof Callback);
    }
}