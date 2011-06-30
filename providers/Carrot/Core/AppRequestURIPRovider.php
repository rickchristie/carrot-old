<?php

namespace Carrot\Core;

class AppRequestURIProvider extends Provider
{
    protected $dependencies = array('request' => 'Carrot\Core\Request@Main');
    
    protected $singletons = array('Main');
    
    public function getMain()
    {
        return new AppRequestURI($this->request);
    }
}