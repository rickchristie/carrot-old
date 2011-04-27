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
 * function that returns the class instance to an identification:
 *
 * <code>
 * $dic->register('\Carrot\Core\Classes\DependencyInjectionContainer@main', function($dic)
 * {
 *    return new \Carrot\Core\Classes\DependencyInjectionContainer();
 * });
 * </code>
 *
 * You can then call it using its identification:
 *
 * <code>
 * $object = $dic->getInstance('\Carrot\Core\Classes\DependencyInjectionContainer@main');
 * </code>
 *
 * You have to use a fully qualified class name plus a configuration name separated
 * by '@' sign as the registration ID. This way you can have different anonymous functions
 * registered on the same class:
 *
 * <code>
 * \Vendor\Namespace\ClassName@configuration_name
 * \Carrot\Library\MySQLDatabase@primary_database
 * \Carrot\Library\MySQLDatabase@backup_database
 * </code>
 *
 * Since this class adheres to the PSR-0 universal autoloader final proposal, it expects
 * at least two namespaces, the vendor name and the namespace (\Vendor\Namespace).
 * 
 * Whenever this file mentions 'package' or 'package name' it meant the combination
 * of Vendor and Namespace, with a starting backslash (\Vendor\Namespace).
 *
 * Dependency registration is done in dependency registration files. Each package name
 * (\Vendor\Namespace) have their own registration file. Registration file paths for
 * each package are determined via a constructor parameter. If no registration file path
 * is defined for the package in question, the DIC will try to find '_dicregistration.php'
 * file at the package's folder.
 *
 * When someone tries to load an item that hasn't been registered yet, this class will
 * try to load the registration file path for the package in question. You can safely
 * get the instance of any item without worrying it has been registered or not.
 *
 * If after loading the registration file the item still doesn't exist, it will
 * try to instantiate the class without any construction parameter. This will throw
 * a warning if your class needs constructor parameter(s).
 *
 * Default behavior is to instantiate new object every time it is needed (transient
 * lifecycle). If you want an object to have a singleton lifecycle, save an instance
 * of the object to the cache before returning it:
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
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class DependencyInjectionContainer
{
    /**
     * @var array List of package names whose dependency registration file has been loaded (if exists).
     */
    protected $dependency_registration_files_loaded = array();
    
    /**
     * @var type List of dependency registration file paths along with the package name they belong to.
     */
    protected $dependency_registration_file_paths = array();
    
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
     * @param string $root_directory Path to the root directory to search for default dependency registration files, without trailing slash.
     * @param array $dependency_registration_file_paths List of dependency registration file paths along with the package name they belong too.
     *
     */
    public function __construct($root_directory, array $dependency_registration_file_paths)
    {
        $this->root_directory = $root_directory;
        $this->dependency_registration_file_paths = $dependency_registration_file_paths;
    }
    
    /**
     * Registers a DIC item.
     *
     * Example usage, registering an instantiation configuration for a Config
     * object:
     *
     * <code> 
     * $dic->register('\Carrot\Library\Config@main', function($dic)
     * {
     *    // Get the request instance to fill in details
     *    $request = $dic->getInstance('\Carrot\Core\Classes\Request@shared');
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
     * qualified name, type the configuration name prefixed by '@'. You can
     * register two different instances of the same class with different
     * configuration name:
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
    }
    
    /**
     * Saves an instance into the cache.
     *
     * Use this method when you are registering an instance that should only be
     * instantiated once (singleton lifecycle):
     *
     * <code> 
     * $dic->register('\Carrot\Library\Config@shared', function($dic)
     * {
     *    // Instantiate the object first
     *    $config = new \Carrot\Library\Config
     *    (
     *        'foo',
     *        'bar'
     *    );
     *
     *    // Save as shared
     *    $dic->saveShared('\Carrot\Library\Config@shared', $config);
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
     * $object = $dic->getInstance('\Carrot\Library\Config@main');
     * </code>
     *
     * If the registration item does not exist, it will try to load the dependency
     * registration file. It will first check for user defined registration file path
     * for the package, if not found, it will try to load '_dicregistration.php' inside
     * the package's folder, for '\Carrot\Library\Config', the default registration
     * file path is:
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
        
        // Load dependency registration file for the package in question if we haven't already
        $this->loadDependencyRegistrationFile($this->getPackageNameFromID($id));
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
     * Root directory is the directory where the packages are contained.
     * It's the path where we look for default _dicregistration.php files.
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
     * @param string $package_name Package name (\Vendor\Namespace). 
     *
     */
    protected function loadDependencyRegistrationFile($package_name)
    {   
        if (in_array($package_name, $this->dependency_registration_files_loaded))
        {
            return;
        }
        
        // Default _dicregistration.php path
        $package_registration_file_path = $this->root_directory . str_ireplace('\\', DIRECTORY_SEPARATOR, $package_name) . DIRECTORY_SEPARATOR . '_dicregistration.php';
        
        // Replace default package path with user defined _dicregistration.php path (if exists)
        if (isset($this->dependency_registration_file_paths[$package_name]))
        {
            $package_registration_file_path = $this->dependency_registration_file_paths[$package_name];
        }
        
        if (file_exists($package_registration_file_path))
        {
            $dic = $this;
            require_once($package_registration_file_path);
        }
        
        $this->dependency_registration_files_loaded[] = $package_name;
    }
    
    /**
     * Validates DIC registration ID.
     *
     * The following rules must be satisfied:
     * 
     *  1. Must have at least two namespaces to form a package name (\Vendor\Namespace).
     *  2. Must be a fully qualified name (with starting backslash).
     *  3. Must have a configuration name after the FQN, separated by '@'.
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
     * Gets the package name (\Vendor\Namespace) from DIC item ID.
     *
     * Package name means the top level namespace (vendor) and the namespace
     * of the class, according to PSR-0 Final Proposal.
     *
     * @param string $id DIC item ID.
     * @return string Package name (\Vendor\Namespace).
     *
     */
    protected function getPackageNameFromID($id)
    {
        $id_exploded = explode('@', $id);
        $namespaces = explode('\\', $id_exploded[0]);
        $fragment_saved = 0;
        $package_name = '';
        
        // Get the first two fragment
        foreach ($namespaces as $fragment)
        {
            if ($fragment_saved == 2)
            {
                break;
            }
            
            if (!empty($fragment))
            {
                $package_name .= '\\' . $fragment;
                $fragment_saved++;
            }
        }
        
        if ($fragment_saved != 2)
        {
            throw new \InvalidArgumentException("Error in getting package name from DIC configuration item, '{$id}' does not have a proper namespace.");
        }
        
        return $package_name;
    }
}