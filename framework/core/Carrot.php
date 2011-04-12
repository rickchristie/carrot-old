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
		$this->response = new Response();
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