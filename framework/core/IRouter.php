<?php

/**
 * Router Interface
 *
 * Copyright (c) 2011 Ricky Christie
 *
 * Licensed under the MIT License.
 *
 * To be implemented by user/framework router class, defines a contract between
 * the Router class with the framework. The Router implementing this interface
 * would mainly be communicating with Carrot (main application) class.
 *
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 */

interface IRouter
{	
	/**
	 * Sets the route, determines the controller to instantiate (and the method to call).
	 *
	 * @param 
	 *
	 */
	public function set_route();
	
	/**
	 * Reset the route
	 *
	 */
	public function set_route_controller_not_found();
	
	
	public function destination_dic_id();
	
	
	public function destination_method();

	
	public function params();
}