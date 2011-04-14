<?php

/**
 * Main carrot class.
 *
 * As many lines of extendend description as you want {@link element}
 * links to an element
 * {@link http://www.example.com Example hyperlink inline link} links to
 * a website. The inline
 * source tag displays function source code in the description:
 * {@source } 
 * 
 * {@link http://www.example.com Read more}
 *
 * @package			package_name
 * @subpackage		sub package name, groupings inside of a project
 * @author 		  	author name <author@email>
 * @copyright		name date
 * @deprecated	 	description
 * @param		 	type [$varname] description
 * @return		 	type description
 * @since		 	a version or a date
 * @todo			phpdoc.de compatibility
 * @var				type	a data type for a class variable
 * @version			version
 */
 
class Carrot
{
	protected $dic;
	protected $request;
	protected $session;
	protected $config;
	protected $response;
	protected $router;
	
	public function __construct($request, $session, $config)
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
		$object = $dic->get_instance('Home');
		
		$object->index();
		
		//$this->response
		
		//$this->response = new Response();
	}
	
	/**
	 * @var type comments
	 */
	public function run()
	{
		
	}
	
	public function exception_handler()
	{
		echo 'exception handled!';
	}
}