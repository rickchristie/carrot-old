<?php

namespace Sample;

use Carrot\Core\Response;

class Welcome
{     
    public function getWelcomeResponse()
    {
        $response = new Response();
        $response->setBody('We are okay! Carrot is running okay!');
        return $response;
    }
}