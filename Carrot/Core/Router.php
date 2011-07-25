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
 * represented by an instance of Dispatch.
 *
 * This class manages routes. Routes are classes that contains
 * the logic and data to translate the current request into an
 * instance of Dispatch. Route classes must implement
 * RouteInterface.
 *
 * The routing process is simple. When doRouting() is called, this
 * class will loop through all registered route classes and calls
 * RouteInterface::route() until one of them returns an
 * instance of Dispatch.
 *
 * Register the route class's object reference into a specific ID:
 *
 * <code>
 * $router->register('RouteID', new ObjectReference('Sample\Route{Main:Transient}'));
 * </code>
 *
 * Don't forget to set the default dispatch object to be returned
 * if there is no matching route:
 *
 * <code>
 * $router->setDispatchForNoMatchingRoute(new Dispatch(
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
     * @var Dispatch The dispatch object to be returned there is no matching route.
     */
    protected $dispatchForNoMatchingRoute = null;
    
    /**
     * @var array List of registered route classes.
     */
    protected $routeRegistrations = array();
    
    /**
     * @var array Contains the list of instantiated route objects.
     */
    protected $routes = array();
    
    /**
     * Sets the dispatch object to be returned when there is no matching route.
     *
     * @param Dispatch $dispatch
     *
     */
    public function setDispatchForNoMatchingRoute(Dispatch $dispatch)
    {
        $this->dispatchForNoMatchingRoute = $dispatch;
    }
    
    /**
     * Registers a route object reference.
     *
     * Route classes must implement RouteInterface. Example
     * registration:
     *
     * <code>
     * $router->register('RouteID', new ObjectReference('Sample\Route{Main:Transient}'));
     * </code>
     *
     * @param string $routeID The route's name, used in two-way routing.
     * @param ObjectReference $objectReference The object reference of the route instance.
     *
     */
    public function registerRoute($routeID, ObjectReference $objectReference)
    {
        $this->routeRegistrations[$routeID] = $objectReference;
    }
    
    /**
     * Instantiates all the registered route into real object instances.
     *
     * This method must be called before doRouting() is called. Will
     * loop through all route registrations and instantiate the route
     * objects via the DIC.
     *
     * @param DependencyInjectionContainer $dic Used to instantiating all the route object references.
     *
     */
    public function instantiateRouteObjects(DependencyInjectionContainer $dic)
    {
        foreach ($this->routeRegistrations as $routeID => $objectReference)
        {
            $route = $dic->getInstance($objectReference);
            
            if (!($route instanceof RouteInterface))
            {
                $className = $objectReference->getClassName();
                throw new RuntimeException("Router error in instantiating routes. Route class '{$className}' does not implement Carrot\Core\Interfaces\RouteInterface.");
            }
            
            $this->routes[$routeID] = $route;
        }
    }
    
    /**
     * Routes the request into a dispatch instance.
     *
     * The actual routing method. Make sure route objects are already
     * instantiated using instantiateRouteObjects() before calling
     * this method.
     * 
     * @return Dispatch The dispatch instance.
     *
     */
    public function doRouting()
    {
        foreach ($this->routes as $routeID => $route)
        {
            $dispatch = $route->route();
            
            if ($this->validDispatch($dispatch))
            {
                return $dispatch;
            }
        }
        
        return $this->getDispatchForNoMatchingRoute();
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
     * Returns the default dispatch instance for no matching route.
     *
     * Throws RuntimeException if the default dispatch instance for no
     * matching route is not set.
     *
     * @see doRouting()
     * @throws RuntimeException
     * @return Dispatch
     *
     */
    protected function getDispatchForNoMatchingRoute()
    {
        if ($this->validDispatch($this->dispatchForNoMatchingRoute))
        {
            return $this->dispatchForNoMatchingRoute;
        }
        
        throw new RuntimeException("Router error in routing request. No matching route was found and there is no default dispatch instance set to be returned for no matching route.");
    }
    
    /**
     * Checks if the variable is an object and an instance of Dispatch.
     *
     * @param mixed $dispatch
     * @return bool
     *
     */
    protected function validDispatch($dispatch)
    {
        return (is_object($dispatch) && $dispatch instanceof Dispatch);
    }
}