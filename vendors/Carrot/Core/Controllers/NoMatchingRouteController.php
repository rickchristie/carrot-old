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
 * No Matching Route Controller
 * 
 * Controller that displays the default 404 page.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Controllers;

class NoMatchingRouteController
{
    /**
     * @var Response Carrot's default response object.
     */
	protected $response;
    
    /**
     * Constructs the No Matching Route Controller.
     *
     * @param Response $response Instance of \Carrot\Core\Classes\Response
     *
     */
	public function __construct(\Carrot\Core\Classes\Response $response)
	{
		$this->response = $response;
	}
	
	/**
     * Returns the default 404 Page not found response.
     *
     * @return Response
     *
     */
	public function index()
	{
		// Get the template, but get it as string with output buffering
        ob_start();
        require(__DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'PageNotFound.php');
        $string = ob_get_clean();
        
        // Create the response and return it to the front controller
        $this->response->setStatus(404);
        $this->response->setBody($string);
        return $this->response;
	}
}