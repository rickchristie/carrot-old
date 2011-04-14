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

class DI_container
{	
	/**
	 * @var array List of dependencies, with the class names as its index.
	 */
	protected $dependency_lists = array();
	
	/**
	 * @var array Lists the type of 
	 */
	protected $dependency_types = array();
	
	/**
	 * @var array Array of references to the instantiated objects.
	 */
	protected $instances;
	
	/**
	 * @var array Array of search paths with trailing slash.
	 */
	protected $search_paths;
	
	/**
	 * @var string Marker phrase for objects.
	 */
	protected $marker_object;
	
	/**
	 * @var string Marker phrase to force new instantiation of objects.
	 */
	protected $marker_object_use_new;
	
	
	public function __construct($search_paths, $preloaded_objects = array(), $marker_object = '&', $marker_object_use_new = '&!')
	{	
		// Validate parameter data types.
		if (!is_array($search_paths) or !is_array($preloaded_objects))
		{
			throw new InvalidArgumentException('Error instantiating DIC. Variables need to be an array.');
		}
		
		// Validate markers
		if (!is_string($marker_object) or !is_string($marker_object_use_new) or empty($marker_object) or empty($marker_object_use_new))
		{
			throw new InvalidArgumentException('Error instantiating DIC. Default markers needs to be string and not empty.');
		}
		
		// Validate the search paths.
		foreach ($search_paths as $path)
		{
			if (!is_dir($path))
			{
				throw new InvalidArgumentException("Error instantiating DIC. Search path is not a valid directory ({$path}).");
			}
		}
		
		// Preloaded objects are automatically inserted
		// to the list of instantiated objects.
		
		$this->instances = $preloaded_objects;
		$this->search_paths = $search_paths;
		$this->marker_object = $marker_object;
		$this->marker_object_use_new = $marker_object_use_new;
	}
	
	/**
	 * Registers a class and its dependencies.
	 *
	 * Dependencies are 
	 *
	 * <code>
	 * // Assuming that 
	 * $dic->register('Foo', array
	 * (
	 *     asd
	 * ));
	 * </code>
	 *
	 * Raises Notice
	 *
	 * @param string $class_name
	 * @param array $dependencies
	 * @return bool TRUE on success, FALSE on failure
	 *
	 */
	public function register($class_name, $dependencies = array(), $marker_object = '', $marker_object_use_new = '')
	{
		// Use default markers if empty/not specified
		if (empty($marker_object))
		{
			$marker_object = $this->marker_object;
		}
		
		if (empty($marker_object_use_new))
		{
			$marker_object_use_new = $this->marker_object_use_new;
		}
		
		// Check parameter data types.
		if (!is_array($dependencies) or !is_string($class_name) or empty($class_name))
		{
			return FALSE;
		}
		
		// Format the dependencies list. We have marker for objects ($marker_objects)
		// and marker for objects that must be re-instantiated ($marker_object_use_new).
		// If any of these markers appear inside the content, we then mark it appropriately:
		//   obj     = object (use instantiated)
		//   obj_new = object (force instantiation)
		//   var     = variable to be passed
		
		$index = 0;
		$this->dependency_lists[$class_name] = array();
		
		foreach ($dependencies as $index => $content)
		{			
			if (is_string($content) && strpos($content, $marker_object))
			{
				$this->dependency_lists[$class_name][$index]['content'] = substr($content, strlen($marker_object));
				$this->dependency_lists[$class_name][$index]['type'] = 'obj';
			}
			else if (is_string($content) && strpos($content, $marker_object_use_new))
			{
				$this->dependency_lists[$class_name][$index]['content'] = substr($content, strlen($marker_object_use_new));
				$this->dependency_lists[$class_name][$index]['type'] = 'obj_new';
			}
			else
			{
				$this->dependency_lists[$class_name][$index]['content'] = $content;
				$this->dependency_lists[$class_name][$index]['type'] = 'var';
			}
			
			$index++;
		}
	}
	
	
	public function instantiate($class_name, $force_instantiation = FALSE)
	{
		// Class name must not be empty.		
		if (empty($class_name) or !is_string($class_name))
		{
			throw new InvalidArgumentException('DIC error in instantiating. Class name empty or not string.');
		}
		
		// If we are allowed to use instantiated
		// objects, first check if we have it stored.
		
		if (!$force_instantiation && array_key_exists($class_name, $this->instances))
		{	
			return $this->instances[$class_name];
		}
		
		$this->load_file($class_name);
		
		// Prepare object dependencies. We cycle
		// through the dependency list and form
		// an array of arguments to be injected.
		
		$ctor_arguments = array();
		
		foreach ($this->dependency_lists[$class_name] as $dependency)
		{
			if ($dependency['type'] == 'obj')
			{
				$ctor_arguments[] = $this->instantiate($dependency['content'], FALSE);
			}
			else if ($dependency['type'] == 'obj_new')
			{
				$ctor_arguments[] = $this->instantiate($dependency['content'], TRUE);
			}
			else if ($dependency['type'] == 'var')
			{
				$ctor_arguments[] = $dependency['content'];
			}
		}
		
		// Instantiate the object using Reflection, begs
		// the question - if you don't have any constructor
		// parameter, why bother using DIC to instantiate
		// the class at all?
		
		if (empty($ctor_arguments))
		{
			$object = new $class_name();
		}
		else
		{
			$reflection = new ReflectionClass($class_name);
			$object = $reflection->newInstanceArgs($ctor_arguments);
		}
		
		// Register the object to our list of instances
		// and return the object.
		
		$this->instances[$class_name] = $object;
		return $object;
	}
	
	public function load_file($file_name)
	{
		// If class already exists, don't try
		// to load the file.
		
		if (!class_exists($file_name))
		{
			$file_exists = FALSE;
			$index = 0;
			$count = count($this->search_paths);
			
			while (!$file_exists)
			{
				$path = $this->search_paths[$index++] . $file_name . '.php';
				$file_exists = file_exists($path);
				
				// If we have iterated through the entire
				// array, and we haven't found the file,
				// throw exception.
				
				if ($index >= $count && !$file_exists)
				{
					throw new RuntimeException("Error in loading class. File ({$file_name}.php) does not exist.");
				}
			}
			
			require($path);
			
			// If class still doesn't exists,
			// throw exception.
			
			if (!class_exists($file_name))
			{
				throw new RuntimeException("DIC error in loading class. File ({$file_name}.php) does not contain the class needed.");
			}
		}
	}
}