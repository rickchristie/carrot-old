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
 * Sample controller class that displays hello world
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace HelloWorld\Controllers;

use Carrot\Core\Response;

class HelloWorldController
{
    /**
     * @var Response The response object to be sent to the FrontController.
     */
    protected $response;
    
    /**
     * Constructs HelloWorldController
     *
     * @param Response $response
     *
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }
    
    /**
     * Says hi to the client.
     * 
     * @return Response The hello world response.
     * 
     */
    public function helloWorld()
    {
        $this->response->setBody('<h1>Hello World!</h1>');
        return $this->response;
    }
}