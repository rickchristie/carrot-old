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
 * HTTP route interface.
 *
 * This interface defines the contract between HTTP route classes
 * and Carrot's router. HTTP route must contain both data and
 * logic necessary to accomplish two-way routing.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Routing\Route;

use Carrot\Routing\Destination,
    Carrot\Routing\HTTPURIInterface;

interface HTTPRouteInterface
{
    /**
     * Routes the HTTP request into a Destination instance.
     * 
     * The router will loop through added routes and call this method
     * on each of them until one of them returns an instance of
     * Destination. Returning an instance of Destination means the
     * route matches the request. If your route object does not match
     * the current request, simply return NULL and the router will
     * move on to the next route object.
     *
     * @see Destination
     * @param HTTPURIInterface $requestHTTPURI
     * @param HTTPURIInterface $baseHTTPURI
     * @return Destination|NULL An instance of Destination if the
     *         route matches the request, NULL otherwise.
     *
     */
    public function route(HTTPURIInterface $requestHTTPURI, HTTPURIInterface $baseHTTPURI);
    
    /**
     * Translates the given arguments into either an absolute or a
     * relative HTTP URI string.
     * 
     * Example absolute URI string return:
     * 
     * <code>
     * http://www.example.org/path/to/file/?query=string
     * </code>
     *
     * Example relative URI string return:
     *
     * <code>
     * /path/to/file/?query=string
     * </code>
     *
     * The HTTP URI to be returned should be already percent encoded.
     * However, it should not be cleaned with htmlentities() or
     * htmlspecialchars() yet.
     * 
     * @param array $args Arguments to generate the URI.
     * @param HTTPURIInterface $baseHTTPURI You can use this copy of
     *        the base HTTP URI to build the HTTPURIInterface object
     *        to be returned.
     * @param bool $absolute If TRUE, this method should return an
     *        absolute URI string, otherwise this method returns a
     *        relative URI string.
     * @return string
     *
     */
    public function getURI(array $args, HTTPURIInterface $baseHTTPURI, $absolute = FALSE);
}