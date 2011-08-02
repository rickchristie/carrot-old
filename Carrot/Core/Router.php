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
 * Register the route class's object reference into a specific ID:
 *
 * <code>
 * $router->registerRouteObject('RouteID', $route);
 * </code>
 *
 * Don't forget to set the default callback object to be returned
 * if there is no matching route:
 *
 * <code>
 * $router->setCallbackForNoMatchingRoute(new Callback(
 *     new ObjectReference('App\Controllers\Default404Controller{Main:Transient}'),
 *     'get404Response'
 * ));
 * </code>
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
     * @var Callback The callback object to be returned there is no matching route.
     */
    protected $callbackForNoMatchingRoute = null;
    
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
     * Registers route object.
     *
     * Route classes must implement RouteInterface. Example
     * registration:
     *
     * <code>
     * $router->registerRouteObject('App.Login', $loginRoute);
     * </code>
     *
     * @param string $routeID The route's ID, used to refer to the route.
     * @param RouteInterface $routeObject The implementation of RouteInterface.
     *
     */
    public function registerRouteObject($routeID, RouteInterface $routeObject)
    {
        $this->routes[$routeID] = $routeObject;
    }
    
    /**
     * Sets the callback object to be returned when there is no matching route.
     *
     * @param Callback $callback
     *
     */
    public function setCallbackForNoMatchingRoute(Callback $callback)
    {
        $this->callbackForNoMatchingRoute = $callback;
    }
    
    /**
     * Routes the request into a callback instance.
     *
     * The actual routing method. Make sure route objects are already
     * instantiated using instantiateRouteObjects() before calling
     * this method.
     * 
     * @return Callback The callback instance.
     *
     */
    public function doRouting()
    {
        foreach ($this->routes as $routeID => $routeObject)
        {
            $callback = $routeObject->route();
            
            if ($this->validCallback($callback))
            {
                $this->activeRouteID = $routeID;
                $this->activeCallback = $callback;
                return $callback;
            }
        }
        
        $callback = $this->getCallbackForNoMatchingRoute();
        $this->activeRouteID = false;
        $this->activeCallback = $callback;
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
    public function getURL($routeID, array $args = array())
    {   
        if (!isset($this->routes[$routeID]))
        {
            throw new InvalidArgumentException("Router error in getting URL. The route with ID '{$routeID}' is not registered.");
        }
        
        return $this->routes[$routeID]->getURL($args);
    }
    
    /**
     * Returns the active route ID.
     *
     * Returns the ID of the route that is responsible for returning
     * the Callback instance after doRouting() is called.
     *
     * Returns null if doRouting() is not called yet.
     *
     * Returns false if doRouting() has been called but there is no
     * matching route, which also means that the callback for no
     * matching route is the active one.
     *
     * @return mixed Either the active route ID, false, or null.
     *
     */
    public function getActiveRouteID()
    {
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
        return $this->activeCallback;
    }
    
    /**
     * Find out the route ID attached to the given RouteInterface instance.
     *
     * Loops through all the registered routes and see if the given
     * object instance is in it. If a match is found, this method
     * returns the route ID for the given RouteInterface instance.
     * Returns null if the route instance given was not registered in
     * the Router.
     *
     * @param RouteInterface $route The route to be checked.
     *
     */
    public function getRouteIDForThisRoute(RouteInterface $routeToMatch)
    {
        foreach ($this->routes as $routeID => $routeObject)
        {
            if ($routeObject === $routeToMatch)
            {
                return $routeID;
            }
        }
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
        if ($this->validCallback($this->callbackForNoMatchingRoute))
        {
            return $this->callbackForNoMatchingRoute;
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