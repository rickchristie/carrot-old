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
 * Sample Routine Object
 * 
 * asd
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Sample;

use Carrot\Core\Response;

class Welcome
{   
    public function getWelcomeResponse()
    {
        ob_start();
        require __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'welcome.php';
        $response = new Response(ob_get_clean());
        return $response;
    }
}