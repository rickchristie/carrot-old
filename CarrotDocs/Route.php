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
 * Used to map all requests to 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Sample;

use InvalidArgumentException;
use Carrot\Core\Interfaces\RouteInterface;
use Carrot\Core\Destination;

class Route implements RouteInterface
{
    /**
     * Route every request to 
     *
     */
    public function translateToDestination($params)
    {
        $segments = $params->appRequestURI->getSegments();
        $topicID = (isset($segments[0])) ? $segments[0] : '';
        $pageID = (isset($segments[1])) ? $segments[1] : '';
        return new Destination('Carrot\SimpleDocs\Controller@Sample', 'getResponse', array($topicID, $pageID));
    }
    
    /**
     * Returns link to the page.
     *
     */
    public function translateToURL($params, array $viewParams)
    {
        if (!isset($viewParams['topicID'], $viewParams['pageID']))
        {
            throw new InvalidArgumentException("Unable to translate to URL, view parameter array does not contain 'topic' and 'pageTitle' index.");
        }
        
        return $params->appRequestURI->getBasePath() . $viewParams['topicID'] . '/' . $viewParams['pageID'];
    }
}