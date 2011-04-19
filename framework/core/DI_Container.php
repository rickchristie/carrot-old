<?php

/**
 * Dependency Injection Container
 *
 * Copyright (c) 2011 Ricky Christie
 *
 * Licensed under the MIT License.
 *
 * This class was modified from the 40 LOC DIC example by Fabien Potencier
 * {@link http://www.slideshare.net/fabpot/dependency-injection-with-php-53}.
 *
 * Container for anonymous functions used to inject dependencies when constructing
 * new objects. Stores each configuration as an ID that can be called. Default
 * behavior is to instantiate new object every time it is needed. This can be
 * changed by declaring an ID as global.
 *
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 */

class DI_Container
{
	/**
	 * @var array List of configuration item ID that's set as global.
	 */
	protected $globals = array();
	
	/**
	 * @var array List of anonymous functions indexed by their ID.
	 */
	protected $functions = array();
	
	/**
	 * @var array List of configuration item class names indexed by their ID.
	 */
	protected $class_names = array();
	
	/**
	 * @var array List of paths to search for the class file.
	 */	
	protected $search_paths = array();
	
	/**
	 * Magically sets the configuration for an ID.
	 *
	 * Defines a configuration item with the specified ID. Here is
	 * an example usage, defines a configuration item with ID
	 * 'fancy_config' that creates a Config class.
	 *
	 * <code> 
	 * $dic->fancy_config = array('Config', function($dic)
	 * {
	 *    return new Config
	 *    (
	 *        'foo',
	 *        'bar',
	 *        $dic->item_dependency
	 *    );
	 * });
	 * </code>
	 *
	 * The anonymous function provided must accept one parameter,
	 * the DIC object itself, and return the instantiated object.
	 * Inside the anonymous function you can use the DIC object
	 * to inject an instance from another configuration item.
	 *
	 * @param string $id ID of the construction configuration.
	 * @param array $array Array that contains the name of the class and the anonymous function.
	 *
	 */
	public function __set($id, array $array)
	{
		if (count($array) != 2)
		{
			throw new InvalidArgumentException("DIC error when registering '{$id}', array must contain class name and an anonymous function.");
		}
		
		if (!is_callable($array[1]))
		{
			throw new InvalidArgumentException("DIC error when registering '{$id}', second array index is not callable.");
		}
		
		$this->functions[$id] = $array[1];
		$this->class_names[$id] = $array[0];
	}
	
	/**
	 * Magically gets the instance of the object.
	 *
	 * If configuration anonymous function with such ID does not exist,
	 * it throws an exception, so make sure the configuration items are
	 * set all at once before getting it. Here's an example of getting
	 * the instance from configuration item ID 'fancy_config'.
	 *
	 * <code>
	 * $object = $dic->fancy_config;
	 * </code>
	 * 
	 * If the configuration item ID has been marked as global using
	 * DI_Controller::set_global() before, this method will return
	 * a reference to the object cache. If the cache for the said ID
	 * doesn't exist yet, it will instantiate a new one and place it
	 * on the cache, to be returned when it's needed again.
	 *
	 * @param string $id Configuration item ID.
	 * @return object Object instantiated with the anonymous function.
	 *
	 */
	public function __get($id)
	{
		if (!isset($this->functions[$id]))
		{
			throw new InvalidArgumentException("DIC error in getting instance, value '{$id}' is not defined.");
		}
		
		$this->load_class_file($this->class_names[$id]);
		
		// Special treatment if it's on list of globals
		if (isset($this->globals[$id]))
		{	
			if (is_object($this->globals[$id]))
			{
				return $this->globals[$id];
			}
			
			$object = $this->functions[$id]($this);
			$this->globals[$id] = $object;
			return $object;
		}
		
		return $this->functions[$id]($this);
	}
	
	/**
	 * Marks a configuration item as global.
	 *
	 * @param string $id Configuration item ID.
	 *
	 */
	public function set_global($id)
	{
		$this->globals[$id] = '';
	}
	
	/**
	 * Add search path to the list.
	 *
	 * @param string $path Path to the search directory, with trailing slash.
	 *
	 */
	public function add_search_path($path)
	{
		$this->search_paths[] = $path;
	}
	
	// ---------------------------------------------------------------
	
	/**
	 * Load the class file.
	 *
	 * Will not try to load if the class already exist. This method
	 * loop through the list of search paths and requires the file
	 * when found. It assumes the name of the file containing the class
	 * is the same as the class name (with .php as its extension).
	 *
	 * @param string $class_name Name of the class to load.
	 * @return bool TRUE if the class is loaded, FALSE if otherwise.
	 *
	 */
	protected function load_class_file($class_name)
	{	
		if (!class_exists($class_name))
		{
			$path = '';
			$found = FALSE;
			
			foreach ($this->search_paths as $folder_path)
			{
				$path = $folder_path . $class_name . '.php';
				
				if (file_exists($path))
				{
					$found = TRUE;
					break;
				}
			}
			
			if ($found)
			{
				require_once($path);
			}
			
			if (class_exists($class_name))
			{
				return TRUE;
			}
			
			return FALSE;
		}
	}
}