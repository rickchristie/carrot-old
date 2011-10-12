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
 * CLI route interface.
 *
 * This interface defines the contract between CLI route classes
 * and Carrot's router. CLI routes does not have the ability to
 * do two-way routing.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Routing\Route;

use Carrot\Core\Routing\Destination;

interface CLIRouteInterface
{
    /**
     * Route the CLI request into an instance of Destination.
     *
     * The router will loop through added routes and call this method
     * on each of them until one of them returns an instance of
     * Destination. Returning an instance of Destination means the
     * route matches the request. If your route object does not match
     * the current request, simply return NULL and the router will
     * move on to the next route object.
     * 
     * @see Destination
     * @return Destination|NULL An instance of Destination if the
     *         route matches the request, NULL otherwise.
     *
     */
    public function route();
}