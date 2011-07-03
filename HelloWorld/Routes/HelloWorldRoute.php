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
 * Maps route to HelloWorldController::helloWorld()
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace HelloWorld\Routes;

use StdClass;
use Carrot\Core\Interfaces\RouteInterface;
use Carrot\Core\Destination;

class HelloWorldRoute implements RouteInterface
{
    /**
     * Translates to HelloWorldController::helloWorld() if we have no application URI segments.
     *
     */
    public function translateToDestination(StdClass $params)
    {   
        if ($params->appRequestURI->getSegmentCount() == 0)
        {
            return new Destination('HelloWorld\Controllers\HelloWorldController@Main', 'helloWorld');
        }
    }
    
    /**
     * Returns base path.
     *
     */
    public function translateToURL(StdClass $params, StdClass $viewParams)
    {
        return $params->appRequestURI->getBasePath();
    }
}