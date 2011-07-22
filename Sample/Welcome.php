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
 * The routine object is the object that contains the routine
 * method. The routine method is a set of routine that you want
 * to be execute when a particular request arrives. Depending on
 * what pattern you use when developing your application, the
 * routine object could be your controller, your view, or just an
 * arbitrary object like this one. What is important is that the
 * routine method returns an instance of Response to the front
 * controller.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Sample;

use Carrot\Core\Response;

class Welcome
{   
    /**
     * Returns the 'welcome' response object.
     *
     * Loads the welcome.php template, creates the response object and
     * returns it to the front controller.
     *
     * @return Response 
     *
     */
    public function getWelcomeResponse()
    {
        ob_start();
        require __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'welcome.php';
        $response = new Response(ob_get_clean());
        return $response;
    }
}