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
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Routing\Config;

use Carrot\Core\Request\RequestInterface,
    Carrot\Core\Routing\URI,
    Carrot\Core\Routing\Route\HTTPRouteInterface,
    Carrot\Core\Routing\Route\CLIRouteInterface,
    Carrot\Core\Routing\Route\BasicHTTPRoute,
    Carrot\Core\Routing\Route\BasicCLIRoute,
    Carrot\Core\DependencyInjection\Container;

class BasicConfig implements ConfigInterface
{
    /**
     * @var RequestInterface The request object.
     */
    protected $request;
    
    /**
     * @var string The 'scheme' part of the base URI, according to
     *      the generic URI syntax in RFC 2396.
     */
    protected $baseURIScheme;
    
    /**
     * @var string The 'authority' part of the base URI, according to
     *      the generic URI syntax in RFC 2396.
     */
    protected $baseURIAuthority;
    
    /**
     * @var string The 'path' part of the base URI, according to the
     *      generic URI syntax in RFC 2396.
     */
    protected $baseURIPath;
    
    /**
     * @var string The 'query' part of the base URI, according to the
     *      generic URI syntax in RFC 2396.
     */
    protected $baseURIQuery;
    
    /**
     * @var array The routes list.
     */
    protected $routes = array();
    
    /**
     * @var Destination The Destination instance to be returned if
     *      there's no matching route.
     */
    protected $noMatchingRouteDestination = NULL;
    
    /**
     * Constructor.
     * 
     * This routing configuration object needs base URI information
     * to work correctly. It will try to guess the base URI scheme,
     * authority, and path if NULL is given for each of them.
     * 
     * <code>
     * $config = new BasicConfig(
     *     $request,
     *     'http',
     *     'example.org',
     *     '/base/path/'
     * );
     * </code>
     * 
     * @param RequestInterface $request The request object.
     * @param string $baseURIScheme The 'scheme' part of the base
     *        URI, according to the generic URI syntax in RFC 2396.
     * @param string $baseURIAuthority The 'authority' part of the
     *        base URI, according to the generic URI syntax in RFC
     *        2396.
     * @param string $baseURIPath The 'path' part of the base URI,
     *        according to the generic URI syntax in RFC 2396.
     * @param string $baseURIQuery The 'query' part of the base URI,
     *        according to the generic URI syntax in RFC 2396.
     *
     */
    public function __construct(Request $request, $baseURIScheme = NULL, $baseURIAuthority = NULL, $baseURIPath = NULL, $baseURIQuery = '')
    {
        $this->request = $request;
        $this->baseURIQuery = $baseURIQuery;
        
        if ($baseURIScheme == NULL)
        {
            $baseURIScheme = $this->guessBaseURIScheme();
        }
        
        if ($baseURIAuthority == NULL)
        {
            $baseURIAuthority =  $this->guessBaseURIAuthority();
        }
        
        if ($baseURIPath = NULL)
        {
            $baseURIPath = $this->guessBaseURIPath();
        }
        
        $this->baseURIScheme = $baseURIScheme;
        $this->baseURIAuthority = $baseURIAuthority;
        $this->baseURIPath = $baseURIPath;
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
        $this->routes[$routeID] = array(
            'listType' => 'object',
            'routeType' => 'CLI',
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
        $this->routes[$routeID] = array(
            'listType' => 'object',
            'routeType' => 'HTTP',
            'object' => $object
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
     * @param string $routeID The ID string to attach the route, must
     *        only refer to ONE route object (CLI or HTTP).
     * @param Reference $reference Refers to the route object that is
     *        being added to the list.
     *
     */
    public function addCLIRouteReference($routeID, Reference $reference)
    {
        $this->routes[$routeID] = array(
            'listType' => 'reference',
            'routeType' => 'CLI',
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
     * @param string $routeID The ID string to attach the route, must
     *        only refer to ONE route object (CLI or HTTP).
     * @param Reference $reference Refers to the route object that is
     *        being added to the list.
     *
     */
    public function addHTTPRouteReference($routeID, Reference $reference)
    {
        $this->routes[$routeID] = array(
            'listType' => 'reference',
            'routeType' => 'HTTP',
            'reference' => $reference
        );
    }
    
    /**
     * Set the Destination instance to be returned by the router if
     * there is a routing failure.
     *
     * Generally speaking, this will be a 404 page, but let's not
     * jump into conclusions.
     *
     * @param Destination $destination 
     *
     */
    public function setNoMatchingRouteDestination(Destination $destination)
    {
        $this->noMatchingRouteDestination = $destination;
    }
    
    /**
     * Get the Destination instance to be returned by the router if
     * there is a routing failure.
     *
     * Generally speaking, this will be a 404 page, but let's not
     * jump into conclusions.
     * 
     * @return Destination
     *
     */
    public function getNoMatchingRouteDestination()
    {
        return $this->noMatchingRouteDestination;
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
        $routeIDs = array();
        
        foreach ($this->routes as $id => $routeArray)
        {
            $routeIDs[$id] = $routeArray['routeType'];
        }
        
        return $routeIDs;
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
        
    }
}