<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Carrot's default routing configuration implementation.
 *
 * Provide shortcuts to the instantiation and registration of
 * basic route objects provided by Carrot.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Routing\Config;

use Exception,
    RuntimeException,
    InvalidArgumentException,
    Carrot\Request\RequestInterface,
    Carrot\Routing\HTTPURI,
    Carrot\Routing\Destination,
    Carrot\Routing\Route\HTTPRouteInterface,
    Carrot\Routing\Route\CLIRouteInterface,
    Carrot\Routing\Route\BasicHTTPRoute,
    Carrot\Routing\Route\BasicCLIRoute,
    Carrot\DependencyInjection\Container;

class BasicConfig implements ConfigInterface
{
    /**
     * @var RequestInterface Represents the application's request.
     */
    protected $request;
    
    /**
     * @var array List of added route configurations.
     */
    protected $routes = array();
    
    /**
     * @var array List of added route IDs and their types. To be used
     *      as a return value for {@see getRouteIDs()}.
     */
    protected $routeIDs = array();
    
    /**
     * @var Destination The Destination instance to be returned when
     *      there is no matching HTTP route.
     */
    protected $noMatchingHTTPRouteDestination;
    
    /**
     * @var Destination The Destination instance to be returned when
     *      there is no matching CLI route.
     */
    protected $noMatchingCLIRouteDestination;
    
    /**
     * Constructor.
     * 
     * @param RequestInterface $request Represents the application's
     *        request.
     *
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }
    
    /**
     * Add a CLIRouteInterface instance to the list of routes,
     * attaching it to the given route ID.
     * 
     * CLI route and HTTP route cannot share the the same route ID.
     * The route ID must only refer to ONE route object.
     * 
     * @param string $routeID The ID string to attach the route, must
     *        only refer to ONE route object (CLI or HTTP).
     * @param CLIRouteInterface $route The route object to add.
     *
     */
    public function addCLIRoute($routeID, CLIRouteInterface $route)
    {
        $this->routeIDs[$routeID] = 'CLI';
        $this->routes[$routeID] = array(
            'type' => 'object',
            'object' => $route
        );
    }
    
    /**
     * Add a HTTPRouteInterface instance to the list of routes,
     * attaching it to the given route ID.
     *
     * CLI route and HTTP route cannot share the the same route ID.
     * The route ID must only refer to ONE route object.
     *
     * @param string $routeID The ID string to attach the route, must
     *        only refer to ONE route object (CLI or HTTP).
     * @param HTTPRouteInterface $route The route object to add.
     *
     */
    public function addHTTPRoute($routeID, HTTPRouteInterface $route)
    {
        $this->routeIDs[$routeID] = 'HTTP';
        $this->routes[$routeID] = array(
            'type' => 'object',
            'object' => $route
        );
    }
    
    /**
     * Add a BasicHTTPRoute instance to the list of routes attaching
     * it to the given route ID.
     * 
     * This method provides a shortcut so that the user doesn't have
     * to instantiate BasicHTTPRoute on his/her own.
     * 
     * @param string $routeID The ID string to attach the route to.
     * @param array $basicHTTPRouteConfig Configuration array, as
     *        defined by BasicHTTPRoute's constructor.
     *
     */
    public function addBasicHTTPRoute($routeID, array $basicHTTPRouteConfig)
    {
        $this->routeIDs[$routeID] = 'HTTP';
        $this->routes[$routeID] = array(
            'type' => 'basicHTTP',
            'config' => $basicHTTPRouteConfig
        );
    }
    
    /**
     * Add a BasicCLIRoute instance to the list of routes attaching
     * it to the given route ID.
     * 
     * This method provides a shortcut so that the user doesn't have
     * to instantiate BasicCLIRoute on his/her own.
     * 
     * @param string $routeID The ID string to attach the route to.
     * @param array $basicCLIRouteConfig Configuration array, as
     *        defined by BasicCLIRoute's constructor.
     *
     */
    public function addBasicCLIRoute($routeID, array $basicCLIRouteConfig)
    {
        $this->routeIDs[$routeID] = 'CLI';
        $this->routes[$routeID] = array(
            'type' => 'basicCLI',
            'config' => $basicCLIRouteConfig
        );
    }
    
    /**
     * Adds the CLIRouteInterface instance being referred to by the
     * given Reference to the list of routes, attaching it to the
     * given route ID.
     *
     * Routes added by reference are later instantiated using the
     * dependency injection container in {@see getRoute()}. No need
     * to check if the class implements CLIRouteInterface since the
     * router will check anyway.
     *
     * Even though your route objects' lifecycle setting may be set
     * to transient, it will only be instantiated once as Carrot's
     * router keeps a cache of instantiated routes. So unless you
     * really need it, it would be less confusing if you set the
     * lifecycle setting of each route reference as 'Singleton'.
     *
     * @param string $routeID The ID string to attach the route, must
     *        only refer to ONE route object (CLI or HTTP).
     * @param Reference $reference Refers to the route object that is
     *        being added to the list.
     *
     */
    public function addCLIRouteReference($routeID, Reference $reference)
    {
        $this->routeIDs[$routeID] = 'CLI';
        $this->routes[$routeID] = array(
            'type' => 'reference',
            'reference' => $reference
        );
    }
    
    /**
     * Adds the HTTPRouteInterface instance being referred to by the
     * given Reference to the list of routes, attaching it to the
     * given route ID.
     *
     * Routes added by reference are later instantiated using the
     * dependency injection container in {@see getRoute()}. No need
     * to check if the class implements HTTPRouteInterface since the
     * router will check anyway.
     *
     * Even though your route objects' lifecycle setting may be set
     * to transient, it will only be instantiated once as Carrot's
     * router keeps a cache of instantiated routes. So unless you
     * really need it, it would be less confusing if you set the
     * lifecycle setting of each route reference as 'Singleton'.
     *
     * @param string $routeID The ID string to attach the route, must
     *        only refer to ONE route object (CLI or HTTP).
     * @param Reference $reference Refers to the route object that is
     *        being added to the list.
     *
     */
    public function addHTTPRouteReference($routeID, Reference $reference)
    {
        $this->routeIDs[$routeID] = 'HTTP';
        $this->routes[$routeID] = array(
            'type' => 'reference',
            'reference' => $reference
        );
    }
    
    /**
     * Set the Destination instance to be returned by the router if
     * there is no matching HTTP route.
     *
     * Generally speaking, this will be a 404 page, but let's not
     * jump into conclusions.
     *
     * @param Destination $destination
     *
     */
    public function setNoMatchingHTTPRouteDestination(Destination $destination)
    {
        $this->noMatchingHTTPRouteDestination = $destination;
    }
    
    /**
     * Get the Destination instance to be returned by the router if
     * there is no matching HTTP route.
     *
     * Generally speaking, this will be a 404 page, but let's not
     * jump into conclusions.
     * 
     * @return Destination
     *
     */
    public function getNoMatchingHTTPRouteDestination()
    {
        return $this->noMatchingHTTPRouteDestination;
    }
    
    /**
     * Set the Destination instance to be returned by the router if
     * there is no matching HTTP route.
     * 
     * @param Destination $destination
     *
     */
    public function setNoMatchingCLIRouteDestination(Destination $destination)
    {
        $this->noMatchingCLIRouteDestination = $destination;
    }
    
    /**
     * Get the Destination instance to be returned by the router if
     * there is no matching HTTP route.
     * 
     * @return Destination
     *
     */
    public function getNoMatchingCLIRouteDestination()
    {
        return $this->noMatchingCLIRouteDestination;
    }
    
    /**
     * Returns an array that contains IDs of all routes that has been
     * added, along with their type (HTTP or CLI).
     * 
     * The index of the returned array is the route ID, while the
     * content is the route type, which could be either 'HTTP' or
     * 'CLI'.
     *
     * <code>
     * $routeIDs = array(
     *     'ForumIndex' => 'HTTP',
     *     'AboutPage' => 'CLI',
     *     'BlogIndex' => 'HTTP',
     *     ...
     * );
     * </code>
     * 
     * @return array
     *
     */
    public function getRouteIDs()
    {
        return $this->routeIDs;
    }
    
    /**
     * Get the route object instance attached the given route ID.
     *
     * You can use the Container instance provided to instantiate
     * route references added via {@see addCLIRouteReference()} and
     * {@see addHTTPRouteReference()}.
     * 
     * @param string $routeID The ID of the route whose object is
     *        being retrieved.
     * @param Container $container To be used in this method to
     *        instantiate route references.
     * @return HTTPRouteInterface|CLIRouteInterface
     *
     */
    public function getRoute($routeID, Container $container)
    {
        if (!isset($this->routes[$routeID]))
        {
            throw new InvalidArgumentException("BasicConfig error in trying to get route object. The route '{$routeID}' is not defined.");
        }
        
        switch ($this->routes[$routeID]['type'])
        {
            case 'object':
                return $this->routes[$routeID]['object'];
            break;
            case 'reference':
                return $container->get($this->routes[$routeID]['reference']);
            break;
            case 'basicHTTP':
                return $this->instantiateBasicHTTPRoute($routeID);
            break;
            case 'basicCLI':
                return $this->instantiateBasicCLIRoute($routeID);
            break;
        }
    }
    
    /**
     * Instantiates a BasicHTTPRoute instance from the given
     * configuration array.
     *
     * @throws RuntimeException When failed to instantiate the route.
     * @param string $routeID The ID of the route being instantiated.
     * @return BasicHTTPRoute
     *
     */
    protected function instantiateBasicHTTPRoute($routeID)
    {
        try
        {
            $route = new BasicHTTPRoute(
                $this->routes[$routeID]['config'],
                $this->request
            );
        }
        catch (Exception $exception)
        {
            $message = $exception->getMessage();
            $file = $exception->getFile();
            $line = $exception->getLine();
            throw new RuntimeException("BasicConfig error in instantiating route '{$routeID}': {$message} - {$file} line {$line}");
        }
        
        return $route;
    }
    
    /**
     * Instantiates a BasicCLIRoute instance from the given
     * configuration array.
     * 
     * @throws RuntimeException When failed to instantiate the route.
     * @param string $routeID The ID of the route being instantiated.
     * @return BasicCLIRoute
     *
     */
    protected function instantiateBasicCLIRoute($routeID)
    {
        try
        {
            $route = new BasicCLIRoute(
                $this->routes[$routeID]['config'],
                $this->request
            );
        }
        catch (Exception $exception)
        {
            $message = $exception->getMessage();
            $file = $exception->getFile();
            $line = $exception->getLine();
            throw new RuntimeException("BasicConfig error in instantiating route '{$routeID}': {$message} - {$file} line {$line}");
        }
        
        return $route;
    }
}