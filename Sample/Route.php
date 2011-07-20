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
 * asdf
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Sample;

use Carrot\Core\Destination;
use Carrot\Core\ObjectReference;
use Carrot\Core\Interfaces\RouteInterface;
use Carrot\Core\AppRequestURI;

class Route implements RouteInterface
{
    protected $appRequestURI;
    
    protected $segments;
    
    public function __construct(AppRequestURI $appRequestURI)
    {
        $this->appRequestURI = $appRequestURI;
        $this->segments = $appRequestURI->getSegments();
    }
    
    public function getDestination()
    {
        if (empty($this->segments))
        {
            return new Destination(new ObjectReference('Sample\Welcome{Main:Transient}'), 'getWelcomeResponse');
        }
    }
    
    public function getURL(array $args)
    {
        return $this->appRequestURI->getBasePath();
    }
}