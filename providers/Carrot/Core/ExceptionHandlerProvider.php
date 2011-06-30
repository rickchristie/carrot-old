<?php

namespace Carrot\Core;

class ExceptionHandlerProvider extends Provider
{
    public function getMain()
    {   
        return new ExceptionHandler($_SERVER['SERVER_PROTOCOL']);
    }
}