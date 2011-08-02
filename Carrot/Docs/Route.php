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
 * Route
 * 
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Docs;

use Carrot\Core\Interfaces\RouteInterface;
use Carrot\Core\AppRequestURI;
use Carrot\Core\Callback;
use Carrot\Core\ObjectReference;
use RuntimeException;

class Route implements RouteInterface
{
    protected $appRequestURI;
    
    protected $segments;
    
    public function __construct(AppRequestURI $appRequestURI)
    {
        $this->appRequestURI = $appRequestURI;
        $this->segments = $appRequestURI->getSegments();
    }
    
    public function route()
    {
        if (isset($this->segments[0]) && $this->segments[0] == 'guides')
        {
            $topicID = (isset($this->segments[1])) ? $this->segments[1] : '';
            $pageID = (isset($this->segments[2])) ? $this->segments[2] : '';
            
            return new Callback(
                new ObjectReference('Carrot\Docs\Controller{Main:Transient}'),
                'getResponse',
                array($topicID, $pageID)
            );
        }
    }
    
    public function getURL(array $args)
    {
        if (!isset($args['topicID'], $args['pageID']))
        {
            throw new InvalidArgumentException("Route error in getting URL, required argument 'topicID' and 'pageID' does not exist.");
        }
        
        return $this->appRequestURI->getBasePath() . 'guides/' . urlencode($args['topicID']) . '/' . urlencode($args['pageID']) . '/';
    }
}