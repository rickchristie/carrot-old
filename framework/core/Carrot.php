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
		// $this->test($this->dic->router->params(0));
		
		echo $this->dic->request->get_base_url();
		
		return $this->dic->response;
	}
	
	public function test($string)
	{
		echo '<pre>', var_dump($string), '</pre>';
	}
	
	public function __destruct()
	{
		restore_error_handler();
		restore_exception_handler();
	}
	
	public function exception_handler()
	{
		echo 'exception handled!';
	}
	
	public function error_handler()
	{
		echo 'error handled!';
	}
	
	// ---------------------------------------------------------------
	
	protected function determine_route()
	{
		
	}
}