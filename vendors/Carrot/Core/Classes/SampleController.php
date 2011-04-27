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
 * Carrot's sample controller. It's a simple controller that serves as a container
 * for defaults, such as the welcome page and the default 404 not found page for
 * the default Router.
 * 
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class SampleController
{
	/**
	 * @var Request Carrot's default request object.
	 */
	protected $request;
	
	/**
	 * @var string Path to the DIC root directory (default is /vendors), without trailing slash.
	 */
	protected $root_directory;
	
	/**
	 * Constructs the sample controller.
	 *
	 * @param Request $request Instance of \Carrot\Core\Classes\Request.
	 * @param string $root_directory Path to the DIC root directory (default is /vendors), without trailing slash.
	 *
	 */
	public function __construct(\Carrot\Core\Classes\Request $request, $root_directory)
	{
		$this->request = $request;
		$this->root_directory = $root_directory;
	}
	
	/**
	 * Loads the default welcome page.
	 *
	 * @return Response
	 *
	 */
	public function welcome()
	{
		// Initialize variables to be used in the template
		$root_directory = $this->root_directory;
		$http_host = $this->request->getServer('HTTP_HOST');
		$base_path = $this->request->getBasePath();
		
		// Get the template, but get it as string with output buffering
		ob_start();
		require(__DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'Welcome.php');
		$string = ob_get_clean();
		
		// Create the response and return it to the front controller
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
		
		// Create the response and return it to the front controller
		$response = new Response($this->request->getServer('SERVER_PROTOCOL'));
		$response->setStatus(404);
		$response->setBody($string);
		return $response;
	}
}