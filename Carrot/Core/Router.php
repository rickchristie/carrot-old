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
 * 
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
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    public function setDestinationForNoMatchingRoute(Destination $destination)
    {
        $this->destinationForNoMatchingRoute = $destination;
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     * @param string 
     *
     */
    public function registerRoute($routeID, ObjectReference $objectReference)
    {
        $this->routeRegistrations[$routeID] = $objectReference;
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
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
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
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
    
    public function getURL($routeID, array $args = array())
    {
        if (!isset($this->routes[$routeID]))
        {
            throw new InvalidArgumentException("Router error in getting URL. The route with ID '{$routeID}' is not registered.");
        }
        
        return $this->routes[$routeID]->getURL($args);
    }
    
    protected function getDestinationForNoMatchingRoute()
    {
        if ($this->validDestination($this->destinationForNoMatchingRoute))
        {
            return $this->destinationForNoMatchingRoute;
        }
        
        throw new RuntimeException("Router error in getting destination. No matching route was found and there is no destination for no matching route.");
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    protected function validDestination($destination)
    {
        return (is_object($destination) && $destination instanceof Destination);
    }
}