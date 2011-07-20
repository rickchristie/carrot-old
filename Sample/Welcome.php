<?php

namespace Sample;

use Carrot\Core\Response;

class Welcome
{
    public function __construct($router, $request)
    {
        $this->router = $router;
        $this->request = $request;
    }
    
    public function getWelcomeResponse()
    {
        ob_start();
        require __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'welcome.php';
        $response = new Response(ob_get_clean());
        return $response;
    }
}