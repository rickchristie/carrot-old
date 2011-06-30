<?php

namespace Carrot\Core;

class FrontControllerProvider extends Provider
{
    protected $dependencies = array
    (
        'router' => 'Carrot\Core\Router@Shared'
    );
    
    public function getMain()
    {   
        return new FrontController($this->router);
    }
}