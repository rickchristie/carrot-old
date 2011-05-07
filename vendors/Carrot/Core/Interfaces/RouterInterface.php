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
 * Router Interface
 *
 * This interface represents the contract between the Router and the framework.
 * The responsibility of the Router class is to return a Destination object to the
 * front controller. Front controller will instantiate a Router and expects it to
 * return an instance of Destination when it calls RouterInterface::getDestination().
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

interface RouterInterface
{
    /**
     * Returns the destination object.
     * 
     * @return \Carrot\Core\Destination
     * 
     */
    public function getDestination();
    
    /**
     * Gets the default destination to go if there's no matching route.
     *
     * @return \Carrot\Core\Destination
     *
     */
    public function getDestinationForNoMatchingRoute();
}