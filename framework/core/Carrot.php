<?php

/**
 * Carrot
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
 * Carrot
 *
 * Main framework object. Accepts Request, Session, and Config objects as
 * construction parameter. Provides error and exception handling after it
 * is instantiated. 
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
	
	public function __construct(Request $request, Session $session, Config $config)
	{
		
	
		$search_paths = array
		(
			$config->item('abspath') . 'controllers/',
			$config->item('abspath') . 'libraries/',
			$config->item('abspath') . 'views/',
			$config->item('abspath') . 'framework/'
		);
		
		$config = array();
		
		$config['Home'] = array
		(
			0 => array('Contents' => 'This is a value', 'Type' => 'Value'),
			1 => array('Contents' => 'Test_view', 'Type' => 'Object'),
			2 => array('Contents' => array
			(
				0 => array('Contents' => 'Value', 'Type' => 'Value'),
				1 => array('Contents' => 'Foo', 'Type' => 'Object:force')
			), 'Type' => 'Array')
		);
		
		$config['Test_view'] = array
		(
			0 => array('Contents' => 'Foo', 'Type' => 'Object')
		);
		
		$config['Foo'] = array
		(
			0 => array('Contents' => 'Bar', 'Type' => 'Object')
		);
		
		$config['Bar'] = array
		(
			0 => array('Contents' => 'Database_mysql', 'Type' => 'Object')
		);
		
		$forbidden = array();
		$singletons = array();
		$transients = array();
		
		$dic = new DI_Container($search_paths, $config, $forbidden, $singletons, $transients);
		
		$dic->load_instance('Config', $config);
		$dic->load_instance('Request', $request);
		$dic->load_instance('Session', $session);
		
		$object = $dic->get_instance('Home', array(2 => array('Contents' => array
			(
				0 => array('Contents' => 'Value', 'Type' => 'Value'),
				1 => array('Contents' => 'Rules', 'Type' => 'Object:force'),
				2 => array('Contents' => array
				(
					0 => array('Contents' => 876548, 'Type' => 'Value'),
					1 => array('Contents' => 'Request', 'Type' => 'Object')
				), 'Type' => 'Array')
			), 'Type' => 'Array')));
		
		$object->index();
		
		//$this->response
		
		//$this->response = new Response();
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