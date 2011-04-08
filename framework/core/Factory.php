<?php

/**
 * Instantiates a library class and injects it with dependencies recursively.
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

class Factory
{
	protected $libraries = array();
	protected $models = array();
	protected $controller = NULL;
	protected $views = array();
	protected $loaded_files = array();
	
	
	public function __construct($config, $db, $router, $request, $session)
	{
		$this->libraries['Config'] = $config;
		$this->libraries['Database'] = $db;
		$this->libraries['Router'] = $router;
		$this->libraries['Request'] = $request;
		$this->libraries['Session'] = $session;
	}
	
	/**
	 * Instantiate a library, model, or view with it's dependencies.
	 *
	 * If the library, model, or view class is already instantiated
	 * before, then the Factory just passes the reference to the object,
	 * meaning that no class will be instantiated twice.
	 *
	 */
	public function instantiate($class_name, $type = 'library')
	{
		// Class name must not be empty
		if (empty($class_name))
		{
			// TODO: Use throw exception, wrong parameter and stuffs
			exit("Factory instantiation error. Cannot instantiate, class name empty ({$type}).");
		}
		
		/*
		|---------------------------------------------------------------
		| LOAD THE FILES AND INSTANTIATE THE CLASS
		|---------------------------------------------------------------
		*/
		
		// Capitalize file name
		$class_name = strtolower($class_name);
		$class_name{0} = strtoupper($class_name{0});
		
		// Load the file and check the class type
		$type_check = FALSE;
		
		switch ($type)
		{
			case 'library':
			
				// Return if the class has already been instantiated
				if (in_array($class_name, $this->libraries))
				{
					return $this->libraries[$class_name];
				}
				
				$this->load_library_file($class_name);
				$type_check = is_subclass_of($class_name, 'Library');
				break;
			
			// ----------------------------------------
				
			case 'model':
				
				// Return if the class has already been instantiated
				if (in_array($class_name, $this->models))
				{
					return $this->models[$class_name];
				}
				
				$this->load_model_file($class_name);
				$type_check = is_subclass_of($class_name, 'Model');
				break;
			
			// ----------------------------------------
				
			case 'controller':
				
				// If it's a controller, it's a special case. No
				// need to check if the class has already been
				// initialized.
				
				$this->load_controller_file($class_name);
				$type_check = is_subclass_of($class_name, 'Controller');
				break;
			
			// ----------------------------------------
				
			case 'view':
				
				// Return if the class has already been instantiated
				if (in_array($class_name, $this->views))
				{
					return $this->views[$class_name];
				}
				
				$this->load_view_file($class_name);
				$type_check = is_subclass_of($class_name, 'View');
				break;
			
			// ----------------------------------------
				
			default:
			
				exit("Factory instantiation error. Type unknown ({$type}).");
				break;
		}
		
		if (!$type_check)
		{
			exit("Factory instantiation error. Type mismatch, {$class_name} is not a {$type}.");
		}
		
		// Instantiate the class
		$object = new $class_name();
		
		// If it's a view object, we need to set the path
		if ($type == 'view')
		{
			$object->set_template_path($this->libraries['Config']->item('abspath') . 'templates/');
		}
		
		/*
		|---------------------------------------------------------------
		| GET THE DEPENDENCIES RECURSIVELY
		|---------------------------------------------------------------
		*/
		
		// Get dependencies
		$to_be_injected = array();
		$library_dependencies = $object->get_library_dependencies();
		$model_dependencies = $object->get_model_dependencies();
		$view_dependencies = $object->get_view_dependencies();
		
		// Prepare library dependencies. Libraries, Models, Controllers, and Views
		// all can have library dependencies.
		
		foreach ($library_dependencies as $library_class_name_d => $object_name)
		{
			// If object instantiation already exists, pass the reference.
			// Otherwise instantiate the library recursively.
			
			if (array_key_exists($library_class_name_d, $this->libraries))
			{
				$to_be_injected[$object_name] = $this->libraries[$library_class_name_d];
				continue;
			}
			
			$to_be_injected[$object_name] = $this->instantiate($library_class_name_d, 'library');
		}
		
		// Only controllers and models can have model dependencies.
		if ($type == 'model' or $type == 'controller')
		{
			foreach ($model_dependencies as $model_class_name_d => $object_name)
			{
				// If model instantiation already exists, pass the reference.
				// Otherwise instantiate the model recursively.
				
				if (array_key_exists($model_class_name_d, $this->models))
				{
					$to_be_injected[$object_name] = $this->models[$model_class_name_d];
					continue;
				}
				
				$to_be_injected[$object_name] = $this->instantiate($model_class_name_d, 'model');
			}
		}
		
		// Only controllers can have view dependencies.
		if ($type == 'controller')
		{
			foreach ($view_dependencies as $view_class_name_d => $object_name)
			{
				// If view instantiation already exists, pass the reference.
				// Otherwise instantiate the view recursively.
				
				if (array_key_exists($view_class_name_d, $this->views))
				{
					$to_be_injected[$object_name] = $this->views[$view_class_name_d];
					continue;
				}
				
				$to_be_injected[$object_name] = $this->instantiate($view_class_name_d, 'view');
			}
		}
		
		/*
		|---------------------------------------------------------------
		| INJECT DEPENDENCIES AND INITIALIZE
		|---------------------------------------------------------------
		*/
		
		$object->load_objects($to_be_injected);
		$object->initialize();
		
		/*
		|---------------------------------------------------------------
		| REGISTER THE OBJECT
		|---------------------------------------------------------------
		*/
		
		switch ($type)
		{
			case 'library':
			
				$this->libraries[$class_name] = $object;
				break;
			
			// ----------------------------------------
				
			case 'model':
			
				$this->models[$class_name] = $object;
				break;
			
			// ----------------------------------------	
			
			case 'controller':
			
				// It's controller, so it's special case.
				$this->controller = $object;
				break;
			
			// ----------------------------------------
				
			case 'view':
			
				$this->views[$class_name] = $object;
				break;
		}
		
		return $object;
	}
	
	// ---------------------------------------------------------------
	
	/**
	 * Load the library file with require().
	 *
	 * It first checks for framework folder. If none found, it checks
	 * libraries folder. If the class already exists, it will not try
	 * to load the file.
	 *
	 */
	protected function load_library_file($class_name)
	{
		if (!class_exists($class_name))
		{
			// Check the framework class first
			$path = $this->libraries['Config']->item('abspath') . 'framework/' . $class_name . '.php';
			
			if (!file_exists($path))
			{
				// Check the user libraries folder
				$path = $this->libraries['Config']->item('abspath') . 'libraries/' . $class_name . '.php';
				
				if (!file_exists($path))
				{
					exit("Unable to load library. File does not exist ({$class_name}.php).");
				}
			}
			
			require($path);
			$this->loaded_files[] = $path;
			
			if (!class_exists($class_name))
			{
				exit("Unable to load library. Class does not exist ({$class_name}) in ({$class_name}.php).");
			}
		}
	}
	
	/**
	 * Load the controller file with require().
	 *
	 * Since the path to the controller file has already been generated
	 * by the Router object, we only need use it. This method assumes that
	 * the Router object has done its job: confirm the existence of the
	 * controller file and change the class name to suit the class naming
	 * convention.
	 *
	 */
	protected function load_controller_file($class_name)
	{
		// Check if the class name provided is the same
		// as the one in Router object.
		
		$class_name_router = $this->libraries['Router']->get_controller_class_name();
		$path = $this->libraries['Router']->get_controller_path();
		
		if ($class_name != $class_name_router)
		{
			exit("Factory instantiation error. Controller class name ({$class_name}) does not match the one provided by Router ({$class_name_router}).");
		}
		
		require($path);
		
		if (!class_exists($class_name))
		{
			exit("Factory instantiation error. Controller file ({$path}) does not contain the required class ({$class_name})");
		}
	}
	
	/**
	 * Load the model file with require().
	 *
	 * It checks the models folder. If the class already exists,
	 * it will not try to load the file.
	 *
	 */
	protected function load_model_file($class_name)
	{
		if (!class_exists($class_name))
		{
			$path = $this->libraries['Config']->item('abspath') . 'models/' . $class_name . '.php';
			
			if (!file_exists($path))
			{
				// TODO: Change the exits in load_*_files functions to trigger errors
				exit("Unable to load model. File does not exist ({$class_name}.php).");
			}
			
			require($path);
			$this->loaded_files[] = $path;
			
			if (!class_exists($class_name))
			{
				exit("Unable to load model. Class does not exist ({$class_name}) in ({$class_name}.php).");
			}
		}
	}
	
	/**
	 * Load the view file with require().
	 *
	 * It checks the views folder. If the class already exists,
	 * it will not try to load the file.
	 *
	 */
	protected function load_view_file($class_name)
	{
		if (!class_exists($class_name))
		{
			$path = $this->libraries['Config']->item('abspath') . 'views/' . $class_name . '.php';
			
			if (!file_exists($path))
			{
				// TODO: Change the exits in load_*_files functions to trigger errors
				exit("Unable to load view. File does not exist ({$class_name}.php).");
			}
			
			require($path);
			$this->loaded_files[] = $path;
			
			if (!class_exists($class_name))
			{
				exit("Unable to load view. Class does not exist ({$class_name}) in ({$class_name}.php).");
			}
		}
	}
}