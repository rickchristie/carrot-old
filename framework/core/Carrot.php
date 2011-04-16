<?php

/**
 * Carrot (Main application class)
 *
 * Copyright (c) 2011 Ricky Christie
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 */

/**
 * Carrot (Main application class)
 *
 * Main framework object. Accepts Request, Session, and DI_Container as
 * construction parameter. Handles error and exception after it is instantiated.
 * 
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 * @todo		
 */
 
class Carrot
{
	protected $dic;
	protected $request;
	protected $session;
	protected $config;
	protected $response;
	protected $router;
	
	/**
	 * Defies imagination, extends boundaries and saves the world ...all before breakfast!
	 *
	 */
	public function __construct(Request $request, Session $session, DI_Container $dic)
	{
		
	}
	
	/**
	 * Runs the controller method.
	 * 
	 * Checks the router if 
	 *
	 */
	public function run()
	{
		
	}
	
	public function exception_handler()
	{
		echo 'exception handled!';
	}
	
	public function error_handler()
	{
		
	}
	
	public function __destruct()
	{
		restore_error_handler();
		restore_exception_handler();
	}
}