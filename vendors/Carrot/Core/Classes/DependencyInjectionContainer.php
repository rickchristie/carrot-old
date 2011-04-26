<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Dependency Injection Container
 * 
 * This class was modified from the 40 LOC DIC example by Fabien Potencier
 * {@link http://www.slideshare.net/fabpot/dependency-injection-with-php-53}.
 *
 * This DIC implementation uses anonymous functions to describe how to create
 * an instance without actually creating it. You use it by registering an anonymous
 * function that returns the class instance to a key/ID:
 *
 * <code>
 * $dic->register('\Carrot\Core\Classes\DependencyInjectionContainer:main', function($dic)
 * {
 *    return new \Carrot\Core\Classes\DependencyInjectionContainer();
 * });
 * </code>
 *
 * You have to use a fully qualified class name plus another name separated by colon
 * as the registration name/ID. This way you can have different anonymous functions
 * registered on the same class:
 *
 * <code>
 * \Carrot\Library\MySQLDatabase:primary_database
 * \Carrot\Library\MySQLDatabase:backup_database
 * </code>
 * 
 * Namespaces must also adhere to the PSR-O Final Proposal, this class expects at
 * least two namespace, the vendor name and the namespace. You don't have to register
 * your dependencies all at once. You can separate them according to their 'bundle',
 * which is a term used to describe a top level namespace after the vendor name.
 *
 * When someone tries to load an item that doesn't exist:
 *
 * <code>
 * // Loading an item that hasn't been registered yet
 * $db = $dic->getInstance('\Carrot\Library\MySQLDatabase:primary');
 * </code>
 *
 * This class will search for registration file paths for the bundle name, in this
 * example, the bundle name is '\Carrot\Library'. If user defined registration file
 * path for that bundle doesn't exist, it will search the namespace folder for 
 * '_dicregistration.php' and load it. This allows you to load any item you want
 * without worrying if it has been registered or not.
 *
 * If after loading the registration file the item still doesn't exist, it will
 * try to instantiate the class without any construction parameter.
 *
 * Default behavior is to instantiate new object every time it is needed, if
 * you want an object to have a singleton lifecycle, save an instance of the
 * object to the cache before returning it:
 *
 * <code>
 * // We don't want another database instance created
 * $dic->register('\Carrot\Library\MySQLDatabase:primary_shared', function($dic)
 * {
 *    // Create an instance
 *    $db = new \Carrot\Library\MySQLDatabase('localhost', 'user', 'pass', 'database');
 *
 *    // Save it into the cache
 *    $dic->saveShared('\Carrot\Library\MySQLDatabase:primary_shared', $db);
 *
 *    return $db;
 * });
 * </code>
 *
 * This would make every subsequent requests to the item return the cached object.
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class DependencyInjectionContainer
{
	/**
	 * @var array List of first bundles whose dependency registration file has been loaded (if exists).
	 */
	protected $bundles_loaded = array();
	
	/**
	 * @var type List of dependency registration file paths along with the bundle they belong to.
	 */
	protected $bundle_registration_file_paths = array();
	
	/**
	 * @var array List of formatted configuration items with their registration ID.
	 */
	protected $items = array();
	
	/**
	 * @var array List of shared objects with their registration ID.
	 */
	protected $shared = array();
	
	/**
	 * @var string Path to the root directory to search for default dependency registration files, without trailing slash.
	 */
	protected $root_directory;
	
	/**
	 * Constructs a DIC object.
	 *
	 * @param string $root_directory Path to the root directory, without trailing slash.
	 * @param array $bundle_registration_file_paths List of dependency registration file paths along with the bundle they belong too.
	 *
	 */
	public function __construct($root_directory, array $bundle_registration_file_paths)
	{
		$this->root_directory = $root_directory;
		$this->bundle_registration_file_paths = $bundle_registration_file_paths;
	}
	
	/**
	 * Registers a DIC item.
	 *
	 * An example, registering \Carrot\Library\Config's parameters:
	 *
	 * <code> 
	 * $dic->register('\Carrot\Library\Config@main', function($dic)
	 * {
	 *    // Get the request instance to fill in details
	 *    $request = $dic->getInstance('\Carrot\Core\Classes\Request:shared');
	 *
	 *    // Returns the instance
	 *    return new \Carrot\Library\Config
	 *    (
	 *        'foo',
	 *        'bar',
	 *        $request->server('REQUEST_URI')
	 *    );
	 * });
	 * </code>
	 *
	 * The DIC item registration ID must use fully qualified name. This is so
	 * that we can determine which namespace it belongs to. After the fully
	 * qualified name, type the item name prefixed by colon (:). You can
	 * register two different instances of the same class.
	 *
	 * <code>
	 * \Carrot\Library\Config@main
	 * \Carrot\Library\Config@sitemap
	 * </code>
	 * 
	 * @param string $id DIC item registration ID.
	 * @param Closure $function Anonymous function that returns the instantiated object.
	 * 
	 */
	public function register($id, \Closure $function)
	{
		if (isset($this->items[$id]))
		{
			throw new \InvalidArgumentException("Error in registering DIC item, ID '{$id}' already exists.");
		}
		
		if (!is_callable($function))
		{
			throw new \InvalidArgumentException("Error in registering DIC item ({$id}), function is not callable.");
		}
		
		if (!$this->validateID($id))
		{
			throw new \InvalidArgumentException("Error in registering DIC item, '{$id}' is not a valid DIC registration ID.");
		}
		
		$this->items[$id]['function'] = $function;
		$this->items[$id]['class_name'] = $this->getClassNameFromID($id);
		$this->items[$id]['bundle'] = $this->getBundleNameFromID($id);
	}
	
	/**
	 * Saves an instance into the cache.
	 *
	 * Use this method when you are registering an instance that should only be
	 * instantiated once (singleton lifecycle):
	 *
	 * <code> 
	 * $dic->register('\Carrot\Library\Config:shared', function($dic)
	 * {
	 *    // Instantiate the object first
	 *    $config = new \Carrot\Library\Config
	 *    (
	 *        'foo',
	 *        'bar'
	 *    );
	 *
	 *    // Save as shared
	 *    $dic->saveShared('\Carrot\Library\Config:shared', $config);
	 *    
	 *    // Return the object
	 *    return $config;
	 * });
	 * </code>
	 *
	 * This will make the object instantiated only once, any subsequent requests
	 * will return the reference to the instantiated object.
	 *
	 * @param string $id DIC item registration ID.
	 * @param mixed $object The object reference to be saved.
	 *
	 */
	public function saveShared($id, $object)
	{	
		if (!isset($this->items[$id]))
		{
			throw new \InvalidArgumentException("Error in saving shared object reference, DIC item '{$id}' doesn't exist.");
		}
		
		if (!is_object($object))
		{
			throw new \InvalidArgumentException("Error in saving shared object reference ({$id}), expected object, '{$object}' given.");
		}
		
		// Validate class name
		$class_name_from_id = $this->getClassNameFromID($id);
		$class_name_from_obj = '\\' . get_class($object);
		
		if ($class_name_from_id !== $class_name_from_obj)
		{
			throw new \InvalidArgumentException("Error in saving shared object reference ({$id}), expected an instance of '{$class_name_from_id}', got '{$class_name_from_obj}' instead.");
		}
		
		$this->shared[$id] = $object;
	}
	
	/**
	 * Returns an instance of a registered DIC item.
	 *  
	 * Use it to get an instance of a registered DIC item:
	 * 
	 * <code>
	 * $object = $dic->getInstance('\Carrot\Library\Config:main');
	 * </code>
	 *
	 * If the registration item does not exist, it will try to load the dependency
	 * registration file. It will first check for user defined path for the bundle.
	 * If not found, it will try to load '_dicregistration.php' inside the namespace's
	 * folder, for the example above, it is:
	 *
	 * \path\to\root\directory\Carrot\Library\_dicregisration.php
	 *
	 * If the item doesn't exist even after it has loaded the registration file,
	 * it will try to instantiate the class without parameters.
	 *
	 * @id string $id DIC item ID.
	 * @return Object 
	 *
	 */
	public function getInstance($id)
	{
		if (!$this->validateID($id))
		{
			throw new \InvalidArgumentException("Error in getting instance, '{$id}' is not a valid DIC item ID.");
		}
		
		// Return shared instance if it exists
		if (isset($this->shared[$id]))
		{
			return $this->shared[$id];
		}
		
		// Load bundle if we haven't already
		$this->loadBundle($this->getBundleNameFromID($id));
		$class_name = $this->getClassNameFromID($id);
		
		// If it doesn't exist, try to instantiate it without parameters
		if (!isset($this->items[$id]))
		{
			if (!class_exists($class_name))
			{
				throw new \InvalidArgumentException("Error in getting instance, DIC item '{$id}' does not exist, class name '{$class_name}' also doesn't exist.");
			}
			
			$object = new $class_name();
			return $object;
		}
		
		// Otherwise instantiate it using saved anonymous function
		$object = $this->items[$id]['function']($this);
		$class_name_from_obj = '\\' . get_class($object);
		
		if ($class_name_from_obj !== $class_name)
		{
			throw new \RuntimeException("Error in getting instance, DIC item '{$id}' does not return the correct instance, expecting an instance of '{$class_name}', got '{$class_name_from_obj}' instead.");
		}
		
		return $object;
	}
	
	/**
	 * Returns the root directory (without trailing slash).
	 *
	 * @return string
	 *
	 */
	public function getRootDirectory()
	{
		return $this->root_directory;
	}
	
	// ---------------------------------------------------------------
	
	/**
	 * Loads a dependency registration file.
	 *
	 * Does not load registration file if it has been loaded already,
	 * will mark a registration file as loaded even if the file
	 * doesn't exist.
	 *
	 * @param string $bundle_name Bundle name (\Vendor\Namespace). 
	 *
	 */
	protected function loadBundle($bundle_name)
	{	
		if (in_array($bundle_name, $this->bundles_loaded))
		{
			return;
		}
		
		// Default _dicregistration.php path
		$bundle_registration_file_path = $this->root_directory . str_ireplace('\\', DIRECTORY_SEPARATOR, $bundle_name) . DIRECTORY_SEPARATOR . '_dicregistration.php';
		
		// Replace default bundle path with user defined _dicregistration.php path (if exists)
		if (isset($this->bundle_registration_file_paths[$bundle_name]))
		{
			$bundle_registration_file_path = $this->bundle_registration_file_paths[$bundle_name];
		}
		
		if (file_exists($bundle_registration_file_path))
		{
			$dic = $this;
			require_once($bundle_registration_file_path);
		}
		
		$this->bundles_loaded[] = $bundle_name;
	}
	
	/**
	 * Validates DIC configuration item ID.
	 *
	 * @param string $id DIC item ID.
	 * @return bool TRUE if valid, FALSE otherwise.
	 *
	 */
	protected function validateID($id)
	{
		$id_exploded = explode('@', $id);
		
		return
		(
			count($id_exploded) == 2 &&
			!empty($id_exploded[0]) &&
			!empty($id_exploded[1]) &&
			$id_exploded[0]{0} == '\\' &&
			substr_count($id_exploded[0], '\\') >= 2
		);
	}
	
	/**
	 * Get the fully qualified class name from DIC item ID.
	 *
	 * @param string $id DIC item ID.
	 * @return string Fully qualified class name.
	 *
	 */
	protected function getClassNameFromID($id)
	{
		$id_exploded = explode('@', $id);
		return $id_exploded[0];
	}
	
	/**
	 * Gets the bundle name (\Vendor\Namespace) from DIC item ID.
	 *
	 * Bundle name means the top level namespace (vendor) and the namespace
	 * of the class, according to PSR-0 Final Proposal. 
	 *
	 * @param string $id DIC item ID.
	 * @return string Bundle name (\Vendor\Namespace).
	 *
	 */
	protected function getBundleNameFromID($id)
	{
		$id_exploded = explode('@', $id);
		$namespaces = explode('\\', $id_exploded[0]);
		$fragment_saved = 0;
		$bundle_name = '';
		
		// Get the first two fragment
		foreach ($namespaces as $fragment)
		{
			if ($fragment_saved == 2)
			{
				break;
			}
			
			if (!empty($fragment))
			{
				$bundle_name .= '\\' . $fragment;
				$fragment_saved++;
			}
		}
		
		if ($fragment_saved != 2)
		{
			throw new \InvalidArgumentException("Error in getting bundle name from DIC configuration item, '{$id}' does not have a proper namespace.");
		}
		
		return $bundle_name;
	}
}