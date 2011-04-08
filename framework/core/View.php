<?php

/**
 * The class to be extended by the user's views.
 *
 * Since views can only have dependencies to libraries, we set the
 * proper variables to private so that the children of this class
 * cannot modify them.
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

class View
{
	protected $__framework_data = array
	(
		'template_path' => '',
		'live' => NULL
	);
	
	protected $library_dependencies = array();
	
	/**
	 * The Factory object will call this function to inject the objects.
	 *
	 */	
	public function load_objects($object_array = array())
	{	
		// We trust that the Factory object has obtained us the necessary
		// objects we declared. If Factory object fails to inject the
		// necessary dependencies, it will stop the script.
		
		foreach ($object_array as $name => $object)
		{
			$this->$name = $object;
		}
	}
	
	/**
	 * Initialize the object, run after all dependencies are injected.
	 *
	 * Since we inject dependencies after the object is constructed, we
	 * have to use another function to initialize the class. Overload
	 * this function to initialize your class.
	 *
	 */
	public function initialize() {}
	
	// ---------------------------------------------------------------
	
	/**
	 * Load a template file. Will check for slashes (/) and folders.
	 *
	 * Convention dictates that all template files must be lowercase.
	 *
	 */
	public function template($template_name, $variables = array())
	{
		$template_names = explode('/', $template_name);
		$folder_path = $this->__framework_data['template_path'];
		$index = -1;
		
		foreach ($template_names as $segment)
		{
			$index++;
			
			if (empty($segment))
			{
				continue;
			}
			
			// Prep the segment for folder name (all lowercase)
			// and file/class name (first character uppercase,
			// the rest lowercase).
			
			$seg_folder_name = strtolower($segment);
			$seg_file_name = $seg_folder_name;
			
			// Check if the file exists, we've found
			// our view. Require it straight away.
			
			$file_path = $folder_path . $seg_file_name . '.php';
			
			if (file_exists($file_path))
			{
				// Extract variables
				
				require($file_path);
				break;
			}
			
			// Otherwise check if it's a directory. If it is,
			// check if we still have another segment. If
			// we don't have, then we failed to find the
			// the template.
			
			$folder_path .= $seg_folder_name . '/';
			
			if (is_dir($folder_path))
			{
				if (isset($template_names[($index+1)]))
				{
					continue;
				}
			}
			
			// If it's not a file, and also not a folder
			// then we really failed.
			
			trigger_error("View class error. Unable to load template. Template file not found ({$template_name})", E_USER_ERROR);
		}
	}
	
	// ---------------------------------------------------------------
	
	public function set_template_path($template_path)
	{
		$this->__framework_data['template_path'] = $template_path;
	}
	
	// ---------------------------------------------------------------
	
	public function get_library_dependencies()
	{
		return $this->library_dependencies;
	}
	
	public function get_view_dependencies()
	{
		return array();
	}
	
	public function get_model_dependencies()
	{
		return array();
	}
}