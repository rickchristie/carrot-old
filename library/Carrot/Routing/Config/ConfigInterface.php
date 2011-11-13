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
 * Routing configuration interface.
 *
 * The routing configuration interface's responsibility is to
 * convert user routing configuration (which could be JSON, XML,
 * or plain PHP method calls, depending on the implementation)
 * into instances of HTTPRouteInterface and CLIRouteInterface.
 * 
 * Although each implementation can have their own non-standard
 * configuration methods, they will have to provide standard ways
 * for the user to explicitly add either the route object
 * directly or a Reference instance which refers to the route
 * object being added.
 * 
 * The Router will call {@see getRouteIDs()}, and loop through
 * the given route IDs. It then gets each route object using
 * {@see getRoute()}, passing the dependency injection container
 * so that the configuration object can convert Reference
 * instances into concrete route objects. Then it will call
 * either {@see HTTPRouteInterface::route()} or
 * {@see CLIRouteInterface::route()} until one of the route
 * objects returns a Destination instance.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Routing\Config;

use Carrot\Routing\Destination,
    Carrot\Routing\Route\HTTPRouteInterface,
    Carrot\Routing\Route\CLIRouteInterface,
    Carrot\DependencyInjection\Container;

interface ConfigInterface
{
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
    public function addCLIRoute($routeID, CLIRouteInterface $route);
    
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
    public function addHTTPRoute($routeID, HTTPRouteInterface $route);
    
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
    public function addCLIRouteReference($routeID, Reference $reference);
    
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
    public function addHTTPRouteReference($routeID, Reference $reference);
    
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
    public function setNoMatchingHTTPRouteDestination(Destination $destination);
    
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
    public function getNoMatchingHTTPRouteDestination();
    
    /**
     * Set the Destination instance to be returned by the router if
     * there is no matching HTTP route.
     * 
     * @param Destination $destination
     *
     */
    public function setNoMatchingCLIRouteDestination(Destination $destination);
    
    /**
     * Get the Destination instance to be returned by the router if
     * there is no matching HTTP route.
     * 
     * @return Destination
     *
     */
    public function getNoMatchingCLIRouteDestination();
    
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
    public function getRouteIDs();
    
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
    public function getRoute($routeID, Container $container);
}