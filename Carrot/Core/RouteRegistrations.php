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
 * Route Registrations
 * 
 * Used to store route registrations values in routes.php,
 * essentially acts as a liason between you and the System. Will
 * use the DependencyInjectionContainer instance to instantiate
 * your custom route classes.
 * 
 * Register your custom route class by their object reference:
 *
 * <code>
 * $routes = new RouteRegistrations($dic);
 * $routes->registerRouteObjectReference(
 *     'App.Login',
 *     new ObjectReference('App\Route\LoginRoute{Main:Transient}')
 * );
 * </code>
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use RuntimeException;
use Carrot\Core\Interfaces\RouteInterface;

class RouteRegistrations
{
    /**
     * @var array Contains the list of registered routes.
     */
    protected $registrations;
    
    /**
     * @var Callback Callback object to be returned when there is no matching route.
     */
    protected $callbackForNoMatchingRoute;
    
    /**
     * @var DependencyInjectionContainer Used to instantiate the route objects.
     */
    protected $dic;
    
    /**
     * @var Request Used to construct BasicRoute.
     */
    protected $request;
    
    /**
     * @var AppRequestURI Used to construct BasicRoute.
     */
    protected $appRequestURI;
    
    /**
     * Constructor.
     * 
     * This class can access the DIC because it needs to dynamically
     * instantiate user's custom routes when requested. This class is
     * constructed by System and shouldn't be instantiated by user.
     * 
     * @param DependencyInjectionContainer $dic Used to instantiate the route objects.
     * 
     */
    public function __construct(Request $request, AppRequestURI $appRequestURI, DependencyInjectionContainer $dic)
    {
        $this->request = $request;
        $this->appRequestURI = $appRequestURI;
        $this->dic = $dic;
    }
    
    /**
     * Registers the route by object reference.
     * 
     * Use this method to register your custom route class. Since your
     * route object will be instantiated using the DIC, you can wire
     * the dependencies of your route object using the DIC.
     *
     * <code>
     * $routes = new RouteRegistrations;
     * $routes->registerRouteObjectReference(
     *     'App.Login',
     *     new ObjectReference('App\Route\LoginRoute{Main:Transient}')
     * );
     * </code>
     * 
     * The array structure for this route type is:
     *
     * <code>
     * $registrations = array(
     *     'type' => 'ObjectReference',
     *     'reference' => $objectReference
     * );
     * </code>
     * 
     * @param string $routeID The route registration ID.
     * @param ObjectReference $objectReference Later will be used by DIC to instantiate the route object.
     *
     */
    public function registerRouteObjectReference($routeID, ObjectReference $objectReference)
    {
        $registrationArray = array(
            'type' => 'ObjectReference',
            'reference' => $objectReference
        );
        
        $this->registrations[$routeID] = $registrationArray;
    }
    
    /**
     * Registers a basic route configuration.
     * 
    // ---------------------------------------------------------------
     * Use this method to register basic routes. Your configuration
     * array will be used to instantiate the BasicRoute instance, so
     * for more information on the configuration array format you
     * should see {@see BasicRoute::__construct()}.
     *
     * <code>
     * $routes->registerBasicRoute('App.Admin.NewPost', array(
     *     'pattern' => '/blog/post/{id}/{slug}/lang/{(fr|de):lang}',
     *     'object' => 'App\Admin\NewPostController{Main:Transient}',
     *     'method' => 'showPost',
     *     'args' => array('@id', '@slug', 1, '@lang'),
     *     'prefix' => '@',
     *     'type' => 'GET'
     * ));
     * </code>
     *
     * TODO: Finish documentation
     * 
     * @param string $routeID The route registration ID.
     * @param array $config The configuration array to send.
     * 
     */
    public function registerBasicRoute($routeID, array $config)
    {
        // TODO: Throw exception if route ID already exists.
        
        $registrationArray = array(
            'type' => 'BasicRoute',
            'config' => $config
        );
        
        $this->registrations[$routeID] = $registrationArray;
    }
    
    /**
     * Get registered route IDs.
     * 
     * The registered route IDs are returned as simple array:
     *
     * <code>
     * $registeredRouteIDs = array(
     *     'App.Login',
     *     'App.Blog.ShowPost',
     *     'App.Blog.IndexPost'
     * );
     * </code>
     *
     * @return array The list of registered route IDs.
     * 
     */
    public function getRegisteredRouteIDs()
    {
        return array_keys($this->registrations);
    }
    
    /**
     * Get route object by ID.
     * 
     * This method is used by the Router object to lazy-load route
     * objects as needed. This way we don't have to initialize every
     * route object we registered every time Carrot starts.
     * 
     * Throws RuntimeException if this class cannot get the route
     * object.
     * 
     * @throws RuntimeException
     * @param string $routeID The ID of the route object to get.
     * @return RouteInterface The route object.
     * 
     */
    public function getRouteObject($routeID)
    {
        $routeInfo = $this->registrations[$routeID];
        
        switch ($routeInfo['type'])
        {
            case 'ObjectReference':
                $routeObject = $this->getRouteObjectByReference($routeID);
            break;
            case 'BasicRoute':
                $routeObject = $this->getBasicRouteInstance($routeID);
            break;
        }
        
        if (!is_object($routeObject) OR !($routeObject instanceof RouteInterface))
        {
            throw new RuntimeException("RouteRegistrations error in retrieving route. Either route '{$routeID}' is not an object or it doesn not implement Carrot\Core\Interfaces\RouteInterface.");
        }
        
        $routeObject->setID($routeID);
        return $routeObject;
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
     * Returns callback for no matching route.
     *
     * @return Callback|null The callback object or null if not set yet.
     *
     */
    public function getCallbackForNoMatchingRoute()
    {
        return $this->callbackForNoMatchingRoute;
    }
    
    /**
     * Get route object registered as object reference.
     *
     * Uses the DIC to instantiate the object reference. This method
     * gets the route object for 'ObjectReference' type registrations.
     * 
     * @param string $routeID The route ID whose object reference we will instantiate.
     * @return RouteInterface The route object.
     *
     */
    protected function getRouteObjectByReference($routeID)
    {
        return $this->dic->getInstance($this->registrations[$routeID]['reference']);
    }
    
    /**
     * Instantiates BasicRoute using the configuration array of the route ID provided.
     *
     * // TODO: More documentation
     *
     */
    protected function getBasicRouteInstance($routeID)
    {
        $config = $this->registrations[$routeID]['config'];
        return new BasicRoute(
            $routeID,
            $config,
            $this->request,
            $this->appRequestURI
        );
    }
}