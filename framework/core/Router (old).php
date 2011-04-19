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
	/**
	 * @var string Path to the directory where we store our controllers. With trailing slash.
	 */
	protected $controllers_folder;
	
	/**
	 * @var array Parameters gathered from the request name.
	 */
	protected $params = array();
	
	/**
	 * @var string Request name, see constructor for more details.
	 */
	protected $request_name;
	
	/**
	 * @var array Request name, exploded to an array of segments.
	 */
	protected $request_name_segments;
	
	/**
	 * @var string The class name of the current active controller (assumed to be the same as the file name).
	 */
	protected $controller_class_name;
	
	/**
	 * @var string Path to the directory that contains the current active controller.
	 */
	protected $controller_folder;
	
	/**
	 * @var string Method to call in the active controller class.
	 */
	protected $controller_method;
	
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
	public function __construct($request_uri, $controllers_folder, $default_request_name, $path_to_framework = '/')
	{	
		// Validate the directories
		if (!is_dir($controllers_folder))
		{
			throw new InvalidArgumentException('Router instantiation error. Folder paths must be a valid directory (with trailing slash).');
		}
		
		if (substr($controllers_folder, -1) != '/')
		{
			$controllers_folder .= '/';
		}
		
		$this->controllers_folder = $controllers_folder;
		unset($controllers_folder);
		
		/*
		|---------------------------------------------------------------
		| FIGURING OUT 'REQUEST NAME'
		|
		| Generally the plain old $_SERVER['REQUEST_URI'] is enough to
		| determine which controller to call. However:
		|
		|	1. $_SERVER['REQUEST_URI'] may be empty. In this case we
		|	   need to replace the request uri with the default from
		|	   config.
		|
		|	2. $_SERVER['REQUEST_URI'] may still contain path to
		|	   framework. Path to framewok must be clipped from the
		|	   request uri if we want to determine the controller.
		|
		|	3. $_SERVER['REQUEST_URI'] may contain query strings. It
		|	   also must be clipped if we want to determine the
		|	   controller.
		|
		| Which is why we need to reformat the request uri to a 'request
		| name', containing only information we need, without the noise.
		|
		|---------------------------------------------------------------
		*/
		
		$request_name = $request_uri;
		
		// Remove query string from request name
		$pos = strpos($request_name, '?');
		
		if ($pos !== FALSE)
		{
			$request_name = substr($request_name, 0, $pos);
		}
		
		// Remove path to framework from the request name.
		// First we make sure that path to framework exists
		// and is located at the start of the path, then
		// we remove it.
		
		$pos = strpos($request_name, $path_to_framework);
		
		if ($pos !== FALSE && $pos === 0)
		{
			$request_name = substr($request_name, strlen($path_to_framework));
		}
		
		unset($pos);
		
		// At this point, if the request uri is empty, that is,
		// it contains no other character than the slash (/),
		// we replace it with the default request name from
		// the Config object.
		
		if (empty($request_name) or $request_name == '/')
		{
			$request_name = $default_request_name;
		}
		
		$this->request_name = $request_name;
		
		/*
		|---------------------------------------------------------------
		| GENERATE REQUEST NAME SEGMENTS
		|---------------------------------------------------------------
		*/
		
		$request_name_expl = explode('/', $request_name);
		$this->request_name_segments = array();
		
		// Fill the uri_segments property, ignore empty segments
		foreach ($request_name_expl as $segment)
		{
			if (!empty($segment))
			{
				// We use rawurldecode instead of urldecode since
				// urldecode will decode '+' sign to space ' '
				$this->request_name_segments[] = rawurldecode($segment);
			}
		}
	}
	
	/**
	 * Determines which active controller class name and the function to call.
	 *
	 */
	public function set_route()
	{
		$request_name_segments = $this->request_name_segments();
		
		/*
		|---------------------------------------------------------------
		| FIND THE CONTROLLER AND FUNCTION
		|---------------------------------------------------------------
		*/
		
		$index = -1;
		$file_path = '';
		$folder_path = $this->controllers_folder;
		$controller_name = array();
		
		foreach ($request_name_segments as $segment)
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
				if (!isset($request_name_segments[($index+1)]))
				{
					$this->controller_method = 'index';
				}
				else
				{
					$this->controller_method = $request_name_segments[$index+1];
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
				if (isset($request_name_segments[($index+1)]))
				{
					continue;
				}
			}
			
			// If we have reach this part then we have a segment
			// that is neither a file/class name or a directory
			// name, which means that we don't know which controller
			// to load.
			
			throw new RuntimeException('Unable to find controller (Default routing behavior).');
		}
		
		$this->controller_name = implode('/', $controller_name);
		
		/*
		|---------------------------------------------------------------
		| DETERMINE THE PARAMETER
		|---------------------------------------------------------------
		*/
		
		$index++;
		$count = count($request_name_segments);
		
		for ($i = $index; $i < $count; $i++)
		{
			$this->params[] = $request_name_segments[$i];
		}
		
		// We have successfully determined the route.
		return TRUE;
	}
	
	public function test()
	{
		throw new Exception('message');
	}
	
	public function set_custom_route($object)
	{
		if (!method_exists($object, 'set_route'))
		{
			exit("Routing error. Custom set route object does not have a set_route() method.");
		}
		
		// Call the custom set route function. If the custom set route
		// method can't find the route, it is accepted agreement that
		// it will throw a RuntimeException, which should bubble up
		// to the caller.
		
		$return = $object->set_route();
		
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
	
	// ---------------------------------------------------------------
	
	public function request_name()
	{
		return $this->request_name;
	}
	
	public function request_name_segments()
	{
		return $this->request_name_segments;
	}
	
	public function controller_class_name()
	{
		return $this->controller_class_name;
	}
	
	public function controller_method()
	{
		return $this->controller_method;
	}
	
	public function controller_folder()
	{
		return $this->controller_folder;
	}
	
	public function parameters()
	{
		return $this->params;
	}
}