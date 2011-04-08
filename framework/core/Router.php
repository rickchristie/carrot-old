<?php

/**
 * The short description
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

class Router
{
	protected $controller_name = '';
	protected $controller_class_name = '';
	protected $controller_path = '';
	protected $controller_method = '';
	protected $params = array();
	protected $config;
	protected $request;
	
	public function __construct($config, $request)
	{
		$this->config = $config;
		$this->request = $request;
	}
	
	/**
	 * Determines which active controller class name and the function to call.
	 *
	 */
	public function set_route()
	{
		$uri_segments = $this->request->uri_segments();
		
		/*
		|---------------------------------------------------------------
		| FIND THE CONTROLLER AND FUNCTION
		|---------------------------------------------------------------
		*/
		
		$index = -1;
		$file_path = '';
		$folder_path = $this->config->item('abspath') . 'controllers/';
		$controller_name = array();
		
		foreach ($uri_segments as $segment)
		{
			$index++;
			
			// Prepare two types of names, because each
			// segment can be the file/class name or the
			// folder name. Convention dictates that folder
			// name must be all lowercase and only the first
			// character of the file name is uppercase.
			
			$seg_folder = strtolower($segment);
			$seg_class_name = strtolower($segment);
			$seg_class_name{0} = strtoupper($segment);
			$controller_name[] = $seg_folder;
			
			// Check if it's a file. If this segment is indeed
			// a controller file, then we have found our controller.
			
			$file_path = $folder_path . $seg_class_name . '.php';
			
			if (file_exists($file_path))
			{
				// Determine function name
				if (!isset($uri_segments[($index+1)]))
				{
					$this->controller_method = 'index';
				}
				else
				{
					$this->controller_method = $uri_segments[$index+1];
				}
				
				$this->controller_class_name = $seg_class_name;
				$this->controller_path = $file_path;
				break;
			}
			
			// Otherwise, if it's not a file name, check if it's
			// a directory name. If it is, check if we have the
			// next index to check as file name.
			
			$folder_path .= $seg_folder . '/';
			
			if (is_dir($folder_path))
			{
				if (isset($uri_segments[($index+1)]))
				{
					continue;
				}
			}
			
			// If we have reach this part then we have a segment
			// that is neither a file/class name or a directory
			// name, which means that we don't know which controller
			// to load.
			
			$this->show_404('Unable to find controller (Default routing behavior).');
		}
		
		$this->controller_name = implode('/', $controller_name);
		
		/*
		|---------------------------------------------------------------
		| DETERMINE THE PARAMETER
		|---------------------------------------------------------------
		*/
		
		$index++;
		$count = count($uri_segments);
		
		for ($i = $index; $i < $count; $i++)
		{
			$this->params[] = $uri_segments[$i];
		}
	}
	
	public function set_custom_route($object)
	{
		if (!method_exists($object, 'set_route'))
		{
			exit("Routing error. Custom set route object does not have a set_route() method.");
		}
		
		// Call the custom set route function
		$return = $object->set_route();
		
		// If the return is string, then show 404 page
		if (is_string($return))
		{
			$this->show_404($return);
		}
		
		// Otherwise, if the return is array, we assume
		// that it is successful and we write the return
		// variables after we check the return.
		
		if
		(
			is_array($return) &&
			array_key_exists('controller_name', $return) &&
			array_key_exists('controller_class_name', $return) &&
			array_key_exists('controller_path', $return) &&
			array_key_exists('controller_method', $return) &&
			array_key_exists('params', $return) &&
			is_array($return['params'])
		)
		{
			$this->controller_name = $return['controller_name'];
			$this->controller_class_name = $return['controller_class_name'];
			$this->controller_path = $return['controller_path'];
			$this->controller_method = $return['controller_method'];
			$this->params = $return['params'];
			
			return;
		}
		
		// Return variable fails the check
		exit('Routing error. Custom set route object fails to return a valid response (Not using default routing behavior).');
	}
	
	public function show_404($message)
	{
		// echo '<pre>', var_dump($view), '</pre>';
		
		exit('404 - you know, file not found. ' . $message);
	}
	
	public function show_error()
	{
		
	}
	
	public function load_default_template($template_name, $variables = array())
	{
		// TODO: Finish variable extracting
		// Check if file exists
		
		$file_path = $this->config->item('abspath') . 'templates/default/' . $template_name . '.php';
		
		if (!file_exists($file_path))
		{
			trigger_error("Router error. Unable to load default template file ({$file_path})", E_USER_ERROR);
		}
		
		require($file_path);
	}
	
	// ---------------------------------------------------------------
	
	public function get_controller_name()
	{
		return $this->controller_name;
	}
	
	public function get_controller_class_name()
	{
		return $this->controller_class_name;
	}
	
	public function get_controller_method()
	{
		return $this->controller_method;
	}
	
	public function get_controller_path()
	{
		return $this->controller_path;
	}
	
	public function get_params()
	{
		return $this->params;
	}
}