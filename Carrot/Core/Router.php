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
 * should be passed to the routine method. These information are
 * what we call 'Destination' and are represented by an instance
 * of Destination.
 *
 * This class manages routes. Routes are classes that contains
 * the logic and data to translate the current request into an
 * instance of Destination. Route classes must implement
 * RouteInterface.
 *
 * The routing process is simple. When getDestination() is called,
 * this class will loop through all registered route classes and
 * calls RouteInterface::getDestination() until one of them
 * returns an instance of Destination.
 *
 * Register the route class's object reference into a specific ID:
 *
 * <code>
 * $router->register('RouteID', new ObjectReference('Sample\Route{Main:Transient}'));
 * </code>
 *
 * Don't forget to set the destination to go to when there is no
 * matching route:
 *
 * <code>
 * $router->setDestinationForNoMatchingRoute(new Destination(
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
     * @var Destination Destination to go to when there is no matching route.
     */
    protected $destinationForNoMatchingRoute = null;
    
    /**
     * @var array List of registered route classes.
     */
    protected $routeRegistrations = array();
    
    /**
     * @var array Contains the list of instantiated route objects.
     */
    protected $routes = array();
    
    /**
     * Loads a route configuration file.
     *
     * The file path given will have access to this object through
     * the $router variable. The file will be loaded from an anonymous
     * function, so the file will only have access to public methods.
     *
     * Use this method to load a file registering all your needed
     * routes. Example usage:
     *
     * <code>
     * $router->loadConfigurationFile('/absolute/path/to/file.php');
     * </code>
     *
     * @param string $filePath Absolute file path to the route configuration file.
     *
     */
    public function loadConfigurationFile($filePath)
    {
        if (!file_exists($filePath))
        {
            throw new InvalidArgumentException("Router error in loading configuration file. The file '{$filePath}' does not exist.");
        }
        
        $loadFile = function($filePath, $router)
        {
            require $filePath;
        };
        
        $loadFile($filePath, $this);
    }
    
    /**
     * Sets the destination to go to when there is no matching route.
     *
     * @param Destination $destination
     *
     */
    public function setDestinationForNoMatchingRoute(Destination $destination)
    {
        $this->destinationForNoMatchingRoute = $destination;
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
     * This method must be called before getDestination() is called.
     * Will loop through all route registrations and gets the route
     * object instances.
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
     * Gets the destination.
     *
     * The actual routing method. Make sure route objects are already
     * instantiated using instantiateRouteObjects() before calling
     * this method.
     * 
     * @return Destination The destination.
     *
     */
    public function getDestination()
    {
        foreach ($this->routes as $routeID => $route)
        {
            $destination = $route->getDestination();
            
            if ($this->validDestination($destination))
            {
                return $destination;
            }
        }
        
        return $this->getDestinationForNoMatchingRoute();
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
     * Gets the destination for no matching route.
     *
     * Throws RuntimeException if the destination for no matching
     * route is not set.
     *
     * @see getDestination()
     * @throws RuntimeException
     * @return Destination
     *
     */
    protected function getDestinationForNoMatchingRoute()
    {
        if ($this->validDestination($this->destinationForNoMatchingRoute))
        {
            return $this->destinationForNoMatchingRoute;
        }
        
        throw new RuntimeException("Router error in getting destination. No matching route was found and there is no destination for no matching route.");
    }
    
    /**
     * Checks if the destination is an object and an instance of Destination.
     *
     * @param mixed $destination
     * @return bool
     *
     */
    protected function validDestination($destination)
    {
        return (is_object($destination) && $destination instanceof Destination);
    }
}