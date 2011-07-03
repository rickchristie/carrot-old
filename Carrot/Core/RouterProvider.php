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
 * Default provider class for Router
 * 
 * Injects an instance of Request and AppRequestURI as the routing
 * parameters. It also runs Router::loadRouteRegistrationFile()
 * to load 'routes.php' located at the framework root folder.
 *
 * >> Notice <<
 * 
 * This is a default provider class for a \Carrot\Core class. DO
 * NOT EDIT THIS FILE DIRECTLY. If you need to replace this
 * provider class with your own, follow this steps:
 *
 * <ol>
 *  <li>Copy and paste this provider class to a new class under
 *  your own namespace.</li>
 *  <li>Modify your custom provider class as needed.</li>
 *  <li>Bind your custom provider class in 'providers.php', this
 *  will make the DIC prioritizes your class and use your
 *  provider class instead.</li>
 * </ol>
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class RouterProvider extends Provider
{
    /**
     * @var array List of dependencies that this provider class needs.
     */
    protected $dependencies = array
    (
        'request' => 'Carrot\Core\Request@Main',
        'appRequestURI' => 'Carrot\Core\AppRequestURI@Main'
    );
    
    /**
     * @var array List of singleton configurations (case-sensitive).
     */
    protected $singletons = array('Main');
    
    /**
     * Returns the instance for 'Main' configuration.
     *
     * This method assumes that this file is located at Carrot's root
     * directory. If you intend to move the routes.php file, you have
     * to replace this provider class with your own.
     * 
     * @return Router
     *
     */
    public function getMain()
    {
        $routingParams = array
        (
            'request' => $this->request,
            'appRequestURI' => $this->appRequestURI
        );
        
        $router = new Router($this->appRequestURI->getString(), $routingParams);
        $carrotRootDirectory = realpath(dirname(dirname(__DIR__)));
        $router->loadRouteRegistrationFile($carrotRootDirectory . DIRECTORY_SEPARATOR . 'routes.php');
        
        return $router;
    }
}