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
 * Welcome Controller
 * 
 * The controller used to display the welcome page.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Controllers;

class WelcomeController
{
    /**
     * @var Request Carrot's default request object.
     */
    protected $request;
    
    /**
     * @var Response Carrot's default response object.
     */
    protected $response;
    
    /**
     * @var string Path to the DIC root directory (default is /vendors), without trailing slash.
     */
    protected $root_directory;
    
    /**
     * Constructs the welcome controller.
     *
     * @param Request $request Instance of \Carrot\Core\Request.
     * @param Response $response Instance of \Carrot\Core\Response.
     * @param string $root_directory Path to the framework's root directory, without trailing slash.
     *
     */
    public function __construct(\Carrot\Core\Request $request, \Carrot\Core\Response $response, $root_directory)
    {
        $this->request = $request;
        $this->response = $response;
        $this->root_directory = $root_directory;
    }
    
    /**
     * Loads the default welcome page.
     * 
     * @return Response
     * 
     */
    public function index()
    {   
        // Initialize variables to be used in the template
        $root_directory = $this->root_directory;
        $http_host = $this->request->getServer('HTTP_HOST');
        $base_path = $this->request->getBasePath();
        
        // Get the template, but get it as string with output buffering
        ob_start();
        require(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'Welcome.php');
        $string = ob_get_clean();
        
        // Return the response to the controller
        $this->response->setBody($string);
        return $this->response;
    }
}