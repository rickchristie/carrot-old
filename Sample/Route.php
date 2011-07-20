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
 * Sample Route
 * 
 * This is a sample route class, it extends the RouteInterface and
 * provides two main methods for two-way routing, one for
 * translating requests to Destination and another to translate
 * arguments into URL.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Sample;

use Carrot\Core\Destination;
use Carrot\Core\ObjectReference;
use Carrot\Core\AppRequestURI;
use Carrot\Core\Interfaces\RouteInterface;

class Route implements RouteInterface
{
    /**
     * @var AppRequestURI
     */
    protected $appRequestURI;
    
    /**
     * @var array Application request URI segments from 
     */
    protected $segments;
    
    /**
     * Constructs the route.
     *
     * @param AppRequestURI $appRequestURI
     *
     */
    public function __construct(AppRequestURI $appRequestURI)
    {
        $this->appRequestURI = $appRequestURI;
        $this->segments = $appRequestURI->getSegments();
    }
    
    /**
     * Gets the destination.
     *
     * Will not return the destination unless the application request
     * URI segments are empty.
     *
     * @return mixed Either Destination or null, depending on the request.
     *
     */
    public function getDestination()
    {
        if (empty($this->segments))
        {
            return new Destination(new ObjectReference('Sample\Welcome{Main:Transient}'), 'getWelcomeResponse');
        }
    }
    
    /**
     * Gets the URL.
     *
     * Will simply return the base path from AppRequestURI.
     *
     * @param array $args The routing arguments.
     *
     */
    public function getURL(array $args)
    {
        return $this->appRequestURI->getBasePath();
    }
}