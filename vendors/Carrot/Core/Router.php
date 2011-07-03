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
 * Carrot's default Router class. To use this class, first you
 * register the routes:
 *
 * <code>
 * $router->registerRoute('blogShow', 'Blog\Routes\BlogShowRoute');
 * $router->registerRoute('pageAbout', 'Blog\Routes\PageAboutRoute');
 * </code>
 *
 * When getDestination() is called, this class will loop through
 * the routes to run RouteInterface::translateToDestination()
 * until one of them returns an instance of Destination.
 * 
 * By default, your route will always be instantiated and run on
 * every request. You can add regex filter as the third argument
 * when registering the route to have the regex matched to the
 * application request URI.
 *
 * For example, if you want your route class to be consulted only
 * when the application request URI starts with '/blog/[...]' you
 * can register it with the following regex:
 * 
 * <code>
 * $router->registerRoute('blogShow', 'Blog\Routes\BlogShowRoute', '|^/blog/|');
 * </code>
 *
 * Notice that we use '|' character as the regex delimiter instead
 * of the usual '/' so that we don't have to escape the slashes.
 *
 * This Router class provides mechanisms to do two-way routing by
 * requiring each route class to also provide
 * RouteInterface::translateToURL() method.
 *
 * For more information on the route class please see the docs on
 * {@see RouteInterface}.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Carrot\Core\Interfaces\RouterInterface;
use Carrot\Core\Interfaces\RouteInterface;
use Carrot\Core\Destination;
use InvalidArgumentException;
use RuntimeException;

class Router implements RouterInterface
{
    /**
     * @var array Array of parameters to be passed to the route classes.
     */
    protected $routingParams;
    
    /**
     * @var Destination The destination instance to return to if there is no matching route.
     */
    protected $destinationForNoMatchingRoute;
    
    /**
     * @var array List of route registrations data.
     */
    protected $routeRegistrations = array();
    
    /**
     * @var array Contains instantiated route classes.
     */
    protected $routeObjects = array();
    
    /**
     * @var string Application request URI string to be checked with the route's regex filter pattern.
     */
    protected $appRequestURIString;
    
    /**
     * Constructs the Router object.
     * 
     * Routing parameters an array of variables that is passed to the
     * translation methods in RouteInterface. It could be anything,
     * from a string to an object. You can change the contents of
     * routing parameters by editing this class's provider class.
     * 
     * @param array $routingParams Array of parameters to be passed to the route classes.
     * @param string $appRequestURIString Application request URI string to be matched with routes's regex filter pattern.
     *
     */
    public function __construct($appRequestURIString, array $routingParams = array())
    {
        $this->appRequestURIString = $appRequestURIString;
        $this->routingParams = (object) $routingParams;
    }
    
    /**
     * Goes through all the routes to get the destination instance.
     * 
     * Loops through all the registered routes and run the
     * RouteInterface::translateToDestination() method. If the method
     * does not return an instance of destination, continue to the
     * next route.
     *
     * If the route is exhausted and there is still no destination,
     * this method will run getDestinationForNoMatchingRoute() method
     * to get the destination instance for no matching route.
     * 
     * @return Destination Instance of destination.
     *
     */
    public function getDestination()
    {
        foreach ($this->routeRegistrations as $routeID => $routeInfo)
        {
            if (!$this->regexFilterMatchRequestURI($routeInfo['regex'], $routeID))
            {
                continue;
            }
            
            $routeObject = $this->getRouteObject($routeID);
            $destination = $routeObject->translateToDestination($this->routingParams);
            
            if (is_object($destination) && $destination instanceof Destination)
            {
                return $destination;
            }
        }
        
        return $this->getDestinationForNoMatchingRoute();
    }
    
    /**
     * Runs RouteInterface::translateURL() of the route object from the route ID provided to get the URL.
     * 
     * Call this method from your views/templates to do two-way
     * routing, as in:
     *
     * <code>
     * <a href="<?php $router->getURL('blogShow', array('id' => 'AB3467')) ?>">Read more &raquo;</a>
     * </code>
     * 
     * This way it decouples your template from your URLs. Your
     * template and views are calling the method only providing
     * information that are required to construct the URL. This way if
     * your URL scheme changes in the future, you only need to update
     * your route class and all the links will still be valid.
     * 
     * @param string $routeID The ID of the route object we want to use for translation.
     * @param array $viewParams Arguments from view/template to be sent to RouteInterface::translateURL().
     * @return string The string returned from RouteInterface::translateToURL().
     *
     */
    public function getURL($routeID, array $viewParams = array())
    {
        $viewParams = (object) $viewParams;
        
        if (!isset($this->routeRegistrations[$routeID]))
        {
            throw new InvalidArgumentException("Router error in getting URL, route '{$routeID}' is not registered.");
        }
        
        $routeObject = $this->getRouteObject($routeID);
        return $routeObject->translateURL($this->routingParams, $viewParams);
    }
    
    /**
     * Sets the destination instance to return when there is no matching route.
     * 
     * This destination instance is returned when this class has gone
     * through all the routes and there is still no destination.
     * 
     * @param Destination $destination The destination instance to return to when there is no matching route.
     *
     */
    public function setDestinationForNoMatchingRoute(Destination $destination)
    {
        $this->destinationForNoMatchingRoute = $destination;
    }
    
    /**
     * Gets the destination instance to be returned when there is no matching route.
     *
     * When getDestination() method is called, this class will go
     * through all the routes until one of them returns an instance
     * of destination. If the routes has been exhausted and there is
     * still no destination, this method will be called to retrieve
     * the route for no matching destination.
     *
     * Throws RuntimeException when the destination for no matching
     * route is not set.
     * 
     * @throws RuntimeException
     * @return Destination Instance of Destination to be returned when there is no matching route.
     *
     */
    public function getDestinationForNoMatchingRoute()
    {
        if (!is_object($this->destinationForNoMatchingRoute) or !($this->destinationForNoMatchingRoute instanceof Destination))
        {
            throw new RuntimeException('Router error in routing. No matching route was found and no destination was set to be returned if there is no matching route.');
        }
        
        return $this->destinationForNoMatchingRoute;
    }
    
    /**
     * Registers a route.
     * 
     * To register a route, you have to provide these information:
     *
     * <ul>
     *  <li>
     *   Route ID. A string used for route identification. This ID
     *   is used in two way routing to refer to the route.
     *  </li>
     *  <li>
     *   Route class name. The name of the class that contains the
     *   route translation logic. The class must implement
     *   RouteInterface.
     *  </li>
     *  <li>
     *   Regex filter. The regex string that will be matched to the
     *   application request URI string. This is optional.
     *  </li>
     * </ul>
     *
     * Example regular route registration:
     *
     * <code>
     * $router->registerRoute('blogShow', 'Blog\Routes\BlogShowRoute');
     * </code>
     *
     * To determine destination, this class will go through all
     * registered route class one by one until one of them returns an
     * instance of Destination. If a route class is registered with a
     * regex filter, this router will run the pattern to the request
     * URI string. If a match is found, the route will be consulted,
     * if not the route will be bypassed.
     *
     * For example, if you want your route class to be consulted only
     * when the application request URI starts with '/blog/[...]'
     * 
     * <code>
     * $router->registerRoute('blogShow', 'Blog\Routes\BlogShowRoute', '|^/blog/|');
     * </code>
     *
     * Notice that we use '|' character as the regex delimiter instead
     * of the usual '/' so that we don't have to escape the slashes.
     * 
     * @param string $routeID Route identification, used to identify route in two way routing.
     * @param string $routeClassName Fully qualified class name of the route.
     * @param string $regexFilter The regex filter to 
     *
     */
    public function registerRoute($routeID, $routeClassName, $routeRegexFilter = null)
    {
        if (isset($this->routeRegistrations[$routeID]))
        {
            throw new InvalidArgumentException("Router error when trying to register a route, route ID '{$routeID}' has already been registered.");
        }
        
        if (substr($routeClassName, 0, 1) != '\\')
        {
            $routeClassName = '\\' . $routeClassName;
        }
        
        if (!class_exists($routeClassName))
        {
            $routeClassName = ltrim($routeClassName, '\\');
            throw new InvalidArgumentException("Router error when trying to register a route, route class {$routeClassName} does not exist.");
        }
        
        $this->routeRegistrations[$routeID] = array
        (
            'class' => $routeClassName,
            'regex' => $routeRegexFilter
        );
    }
    
    /**
     * Loads a route registration file.
     * 
     * The route file will have access to the Router object via the
     * $router variable. You can use this file to register your
     * routes. The alternative way to register routes will be to edit
     * the provider class for this Router class.
     *
     * Throws InvalidArgumentException if the file doesn't exist.
     * 
     * @throws InvalidArgumentException
     * @param string $filePath Absolute file path to the route file.
     *
     */
    public function loadRouteRegistrationFile($filePath)
    {
        $requireRouteFile = function($filePath, $router)
        {
            require $filePath;
        };
        
        if (!file_exists($filePath))
        {
            throw new InvalidArgumentException("Router error when trying to load route file, '{$filePath}' doesn't exist.");
        }
        
        $requireRouteFile($filePath, $this);
    }
    
    /**
     * Runs the regex to application request URI and returns the result.
     *
     * If the regex is empty (null), then this method assumes that the
     * route is to be consulted on every request, so it returns true.
     *
     * This method will throw a RuntimeException if the regex string
     * is invalid.
     * 
     * @throws RuntimeException
     * @param string $regex Regex string to be run.
     * @param string $routeID ID of the route that has the regex.
     * @return bool True if matches, false if no match.
     *
     */
    protected function regexFilterMatchRequestURI($regex, $routeID)
    {
        if (empty($regex))
        {
            return true;
        }
        
        try
        {
            $match = preg_match($regex, $this->appRequestURIString);
        }
        catch (Exception $exception)
        {
            throw new RuntimeException("Router error in getting destination, '{$regex}' from '{$routeID}' is not a valid pattern.");
        }
        
        return $match;
    }
    
    /**
     * Gets the route object registered to a specific route ID.
     * 
     * Returns the cache stored in $routeObjects class property by
     * default. If the cache does not exist it will create the route
     * object on the fly.
     * 
     * @param string $routeID The route ID whose object is needed.
     * @return RouteInterface The route object.
     *
     */
    protected function getRouteObject($routeID)
    {
        if (!isset($this->routeObjects[$routeID]))
        {
            $this->routeObjects[$routeID] = new $this->routeRegistrations[$routeID]['class']();
            
            if (!($this->routeObjects[$routeID] instanceof \Carrot\Core\Interfaces\RouteInterface))
            {
                $routeClassName = ltrim($this->routeRegistrations[$routeID]['class'], '\\');
                throw new InvalidArgumentException("Router error when trying to get a route object, route class {$routeClassName} is not an implementation of Carrot\Core\Interfaces\RouteInterface.");
            }
            
        }
        
        return $this->routeObjects[$routeID];
    }
}