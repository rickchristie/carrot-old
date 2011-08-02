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
 * Route Registrations
 * 
 * Value object. Used to store route registrations values in
 * routes.php, essentially acts as a liason between you and the
 * System. Will create the appropriate array structure to be used
 * later by the System to build the Router.
 * 
 * Register your custom route class by their object reference:
 *
 * <code>
 * $routes = new RouteRegistrations;
 * $routes->registerRouteObjectReference(
 *     'App.Login',
 *     new ObjectReference('App\Route\LoginRoute{Main:Transient}')
 * );
 * </code>
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Carrot\Core\Interfaces\RouteInterface;

class RouteRegistrations
{
    /**
     * @var array Contains the list of registered routes.
     */
    protected $registrations;
    
    /**
     * Registers the route by object reference.
     * 
     * Use this method to register your custom route class. Since your
     * route object will be instantiated using the DIC, you can wire
     * the dependencies of your route object using the DIC.
     *
     * <code>
     * $routes = new RouteRegistrations;
     * $routes->registerRouteObjectReference(
     *     'App.Login',
     *     new ObjectReference('App\Route\LoginRoute{Main:Transient}')
     * );
     * </code>
     * 
     * 
     * @param string $routeID The route ID to be used.
     * @param ObjectReference $objectReference Later will be used by DIC to instantiate the route object.
     *
     */
    public function registerRouteObjectReference($routeID, ObjectReference $objectReference)
    {
        $registrationArray = array(
            'type' => 'ObjectReference',
            'reference' => $objectReference
        );
        
        $this->registrations[$routeID] = $registrationArray;
    }
    
    /**
     * Returns the registered array.
     *
     * The array returned is a one dimensional array with the route
     * IDs as the index, and a route information array as the
     * contents:
     *
     * <code>
     * $registrations = array(
     *     'App.RouteID.A' => $routeInfoA,
     *     'App.RouteID.B' => $routeInfoB
     * );
     * </code>
     *
     * The structure of the route information array depends on the
     * type of the registration. If it is a custom route object
     * registered using registerRouteObjectReference(), the structure
     * is as follows:
     *
     * <code>
     * $routeInfo = array(
     *     'type' => 'ObjectReference',
     *     'reference' => $objectReference
     * );
     * </code>
     *
     * @return array The list of registered routes.
     *
     */
    public function get()
    {
        return $this->registrations;
    }
}