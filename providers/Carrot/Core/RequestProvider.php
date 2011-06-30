<?php

namespace Carrot\Core;

class RequestProvider extends Provider
{
    protected $singletons = array('Main');
    
    public function getMain()
    {
        return new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE, $_REQUEST, $_ENV);
    }
}