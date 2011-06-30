<?php

namespace Carrot\Core;

class FrontControllerProvider extends Provider
{
    protected $dependencies = array
    (
        'router' => 'Carrot\Core\Router@Main'
    );
    
    public function getMain()
    {   
        return new FrontController($this->router);
    }
}