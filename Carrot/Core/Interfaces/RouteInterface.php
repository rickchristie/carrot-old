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
 * the Router. You will have to provide two methods for two-way
 * routing: one for translating requests to an instance of
 * Callback, the other for translating arguments from view into
 * a URL.
 *
 * Please note that a Route class is defined as a class that
 * contains both the data and logic required to represent one
 * specific two-way routing. This means if your route class needs
 * dependencies, you will have to inject it yourself by
 * configuring the DIC.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

interface RouteInterface
{   
    /**
     * Routes the current request into a Callback instance.
     * 
     * The Router will loop through registered route objects and call
     * this method when routing. If your route object cannot route
     * the current request, simply return nothing and the Router will
     * move on to the next route object.
     * 
     * @see \Carrot\Core\Callback
     * @return mixed Either an instance of \Carrot\Core\Callback or null.
     * 
     */
    public function route();
    
    /**
     * Translates arguments from view into a relative path to this route.
     * 
     * The relative path should from this framework's root. You must
     * return a relative path so that when your view calls
     * \Carrot\Core\Router::getURL() he/she can specify whether he
     * wants an absolute URL or just a relative path.
     * 
     * The base path prefix will be added by the Router object, so you
     * only have to return the path relative from Carrot's index.php
     * file. For example, if your base path is '/carrot/':
     *
     * <code>
     * app/login/ => /carrot/app/login/
     * blog/post/?variable=value => /carrot/blog/post/?variable=value
     * </code>
     *
     * Alternatively, if the user specifies to that returned URL
     * should be absolute, the base URL prefix will be appended
     * instead:
     *
     * <code>
     * app/login/ => http://example.com/carrot/app/login/
     * blog/post/?variable=value => http://example.com/carrot/blog/post/?variable=value
     * </code>
     *
     * The base URL and base path values are taken from the
     * Carrot\Core\AppRequestURI{Main:Singleton} instance. You can
     * change it by editing its DIC configuration.
     * 
     * @return string Relative path to this route from the framework's root.
     *
     */
    public function getRelativePath(array $args);
    
    /**
     * Set the route ID.
     * 
     * After route registration is done, when the route object is
     * instantiated, this method will be called by
     * \Carrot\Core\RouteRegistrations. This method was added so that
     * routes can become aware of their own route ID.
     *
     * @param string $id The route object's route registration ID.
     * 
     */
    public function setID($id);
}