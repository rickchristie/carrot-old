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
 * This interface represents the contract between the Router and the front
 * controller (index.php). The responsibility of the Router class is to return
 * a Destination object to the front controller. Front controller will instantiate
 * a Router, tell it to load /routes.php file and expects it to return an instance
 * of Destination when it calls RouterInterface::getDestination().
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
     * @return \Carrot\Core\Classes\Destination
     * 
     */
    public function getDestination();
    
    /**
     * Loads a file that defines routes.
     *
     * Front controller will call this method with a file path to \routes.php
     * as the argument. When you write your own custom Router class, it's up to
     * you whether to use the file or not.
     *
     * @param string $path Absolute path to /routes.php.
     *
     */
    public function loadRoutesFile($path);
    
    /**
     * Gets the default destination to go if there's no matching route.
     *
     * @return \Carrot\Core\Classes\Destination
     *
     */
    public function getDestinationForNoMatchingRoute();
    
    /**
     * Sets the default desintaion to go if there's no matching route.
     *
     * @param Destination $destination
     *
     */
    public function setDestinationForNoMatchingRoute(\Carrot\Core\Classes\Destination $destination);
}