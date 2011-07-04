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
 * Maps route to Controller::displayGuide()
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Guide;

use StdClass;
use Carrot\Core\Interfaces\RouteInterface;
use Carrot\Core\Destination;

class Route implements RouteInterface
{
    /**
     * Route to Controller::displayGuide() if the first segment is 'guide'.
     *
     */
    public function translateToDestination(StdClass $params)
    {   
        if (strtolower($params->appRequestURI->getSegment(0)) == 'guide')
        {
            $segments = array_slice($params->appRequestURI->getSegments(), 1);
            return new Destination('Carrot\Guide\Controller@Main', 'getGuideResponse', array($segments));
        }
    }
    
    /**
     * Returns link to guide.
     *
     */
    public function translateToURL(StdClass $params, StdClass $viewParams)
    {
        return $params->appRequestURI->getBasePath() . 'guide/';
    }
}