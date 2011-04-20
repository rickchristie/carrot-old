<?php

/**
 * Carrot (Main application class)
 *
 * Copyright (c) 2011 Ricky Christie
 *
 * Licensed under the MIT License.
 *
 * Main framework object. Accepts Request, Session, URL and DI_Container as
 * construction parameter. Handles error and exception after it is instantiated.
 *
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 */
 
class Carrot
{
	protected $dic;
	protected $router;
	
	/**
	 * Constructs Carrot object.
	 * 
	 * 
	 * 
	 */
	public function __construct(DI_Container $dic)
	{
		//set_exception_handler(array($this, 'exception_handler'));
		//set_error_handler(array($this, 'error_handler'));
		
		$this->dic = $dic;
		$this->router = $this->dic->router;
	}
	
	/**
	 * Runs the controller method.
	 * 
	 * Checks the router if 
	 *
	 * @return Response
	 *
	 */
	public function dispatch()
	{
		$destination = $this->set_route();
		
		return $this->dic->response;
	}
	
	public function __destruct()
	{
		restore_error_handler();
		restore_exception_handler();
	}
	
	// ---------------------------------------------------------------
	
	protected function get_destination(Application_Request_URI $application_request_uri)
	{
		return $this->set_route($application_request_uri);
	}
}