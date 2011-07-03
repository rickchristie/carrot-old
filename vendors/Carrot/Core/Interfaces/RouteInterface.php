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

use StdClass;

interface RouteInterface
{
    /**
     * Translates the current request into a destination.
     * 
     * Routing parameters are provided by \Carrot\Core\Router class.
     * By default it should provide an instance of
     * \Carrot\Core\Request and Carrot\Core\AppRequestURI. You can
     * change routing parameters by injecting different variables in
     * the router class's provider.
     * 
     * Carrot's default router class will call your method when
     * routing a request (provided your route is registered with the
     * appropriate regex string). Based on information in routing
     * parameters, if the method logic an route the current request,
     * return an instance of \Carrot\Core\Destination. If the method
     * logic can't route the current request return null. 
     * 
     * @see \Carrot\Core\Destination
     * @param StdClass $params Routing parameters in a container object.
     * @return mixed Either an instance of \Carrot\Core\Destination or null.
     *
     */
    public function translateToDestination(StdClass $params);
    
    /**
     * Translates the view parameters into a valid string URL.
     * 
     * This method is called by the router class when your view or
     * template intends to do two way routing. The arguments sent
     * from view should the required information to construct a valid
     * URL.
     *
     * @param StdClass $params Routing parameters in a container object.
     * @param StdClass $viewParams Parameters sent from view inside a container object
     * @return string Valid URL.
     *
     */
    public function translateToURL(StdClass $params, StdClass $viewParams);
}