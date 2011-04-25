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
 * Sample Controller
 *
 * Controller's responsibility is to return an instance of Response to the front
 * controller. This is a sample controller, it doesn't have dependencies to
 *
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

class SampleController
{
	/**
	 * @var Response Response object to send back.
	 */
	protected $request;
	
	/**
	 * Constructs default PageNotFound controller.
	 *
	 * @param Response Response instance.
	 *
	 */
	public function __construct(\Carrot\Core\Classes\Request $request)
	{
		$this->request = $request;
	}
	
	public function welcome()
	{
		// Get the template, but get it as string with output buffering
		ob_start();
		require(__DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'Welcome.php');
		$string = ob_get_clean();
		
		$response = new Response($this->request->getServer('SERVER_PROTOCOL'));
		$response->setBody($string);
		return $response;
	}
	
	/**
	 * Returns the default 404 Page not found response.
	 *
	 * @return Response
	 *
	 */
	public function pageNotFound()
	{
		// Get the template, but get it as string with output buffering
		ob_start();
		require(__DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'PageNotFound.php');
		$string = ob_get_clean();
		
		// Create the request and return it
		$response = new Response($this->request->getServer('SERVER_PROTOCOL'));
		$response->setStatus(404);
		$response->setBody($string);
		return $response;
	}
}