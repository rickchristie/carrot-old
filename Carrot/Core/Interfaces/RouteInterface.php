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
 * Route Interface
 *
 * This interface defines a contract between your route class and
 * Carrot's default Router. Please note that if you choose to use
 * another RouterInterface implementation you might not need to
 * implement this interface.
 *
 * Carrot's default router does two-way routing so you will have
 * to provide two methods, one for translating request to an
 * instance of Destination, the other method is to translate
 * calls from views to valid URLs. Your view/template provides the
 * router with information required to construct the URL and the
 * router will relay that information to you via translateURL()
 * method.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

interface RouteInterface
{   
    /**
     * Routes the current request into a destination.
     * 
     *  
     * 
     * @see \Carrot\Core\Destination
     * @return mixed Either an instance of \Carrot\Core\Destination or null.
     * 
     */
    public function getDestination();
    
    /**
     * Translates arguments from view into a valid string URL.
     * 
     * 
     * 
     * @return string Valid URL.
     *
     */
    public function getURL(array $args);
}