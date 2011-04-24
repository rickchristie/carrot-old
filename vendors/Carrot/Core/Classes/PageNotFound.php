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
 * Page Not Found
 * 
 * Default page not found controller. You can replace this class with your
 * very own page not found controller by editing the routes.php file. Use
 * Router::setDestinationForNoMatchingRoute() to change the default destination
 * to go to when there is no matching route.
 * 
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class PageNotFound
{
	/**
	 * @var Response Response object to send back.
	 */
	protected $response;
	
	/**
	 * Constructs default PageNotFound controller.
	 *
	 * @param Response Response instance.
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
		ob_start();
		require(__DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'PageNotFound.php');
		$string = ob_get_clean();
		
		$this->response->setStatus(404);
		$this->response->setBody($string);
		return $this->response;
	}
}