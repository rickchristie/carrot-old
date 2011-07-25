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
 * translating requests to an instance of Dispatch and another to
 * translate arguments from view into URL.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Sample;

use Carrot\Core\Dispatch;
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
     * Routes the request into a dispatch instance.
     *
     * Will not return the dispatch unless the application request URI
     * segments are empty.
     *
     * @return mixed Either Dispatch or null, depending on the request.
     *
     */
    public function route()
    {
        if (empty($this->segments))
        {
            return new Dispatch(new ObjectReference('Sample\Welcome{Main:Transient}'), 'getWelcomeResponse');
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