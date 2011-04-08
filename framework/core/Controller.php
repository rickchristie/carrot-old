<?php

/**
 * The class to be extended by the user's controllers.
 *
 * Since controllers can have dependencies to libraries, models, and 
 * views, all of them all left untouched.
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

class Controller
{
	protected $library_dependencies = array();
	protected $view_dependencies = array();
	protected $model_dependencies = array();
	
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
	
	public function get_library_dependencies()
	{
		return $this->library_dependencies;
	}
	
	public function get_view_dependencies()
	{
		return $this->view_dependencies;
	}
	
	public function get_model_dependencies()
	{
		return $this->model_dependencies;
	}
}