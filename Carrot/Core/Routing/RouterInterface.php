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
 * Carrot's router interface.
 * 
 * This is the interface that masks interaction between your
 * classes and Carrot's Router. Like the dependency injection
 * container, Carrot's Router is an internal class and cannot be
 * replaced. However, unlike the Container, the Router has
 * methods that the user classes needs to access directly. This
 * interface provides the contract for those methods.
 *
 * In your code's interaction with the Carrot's router, you must
 * only use methods that are defined in this interface. Router
 * methods that aren't defined in this interface must be
 * considered as IMPLEMENTATION DETAIL, and thus must not be
 * relied upon.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Routing;

interface RouterInterface
{
    /**
     * Generate URI string from the given HTTP route ID and
     * arguments.
     *
     * This is the reverse routing method, useful in templates and
     * views where you wanted to generate URIs.
     *
     * @throws InvalidArgumentException If the route ID does not
     *         exist, or if the route is a CLI route.
     * @param string $routeID The ID of the route to generate the
     *        URI we wanted.
     * @param array $args Associative array, contains argument names
     *        and their values.
     * @param bool $absolute If TRUE, will ask the route to generate
     *        absolute URI, relative URI otherwise.
     *
     */
    public function getURI($routeID, array $args = array(), $absolute = FALSE);
    
    /**
     * Gets the list of route IDs, returns the same variable as
     * {@see Config\ConfigInterface::getRouteIDs()}.
     *
     * @return array
     *
     */
    public function getRouteIDs();
    
    /**
     * Gets the currently active route ID.
     *
     * @return string|NULL Currently active route ID, NULL if the
     *         routing process is not completed yet, FALSE if there
     *         is an error in routing.
     *
     */
    public function getActiveRouteID();
}