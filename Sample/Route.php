<?php

namespace Sample;

use Carrot\Core\Destination;
use Carrot\Core\ObjectReference;
use Carrot\Core\Interfaces\RouteInterface;

class Route implements RouteInterface
{
    public function getDestination()
    {
        return new Destination(new ObjectReference('Sample\Welcome{Main:Transient}'), 'getWelcomeResponse');
    }
    
    public function getURL(array $args)
    {
        return '/';
    }
}