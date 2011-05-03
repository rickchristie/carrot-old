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
 * $dic->register('\Carrot\Core\DependencyInjectionContainer@main', function($dic)
 * {
 *    return new \Carrot\Core\DependencyInjectionContainer();
 * });
 * </code>
 *
 * You can then call it using its identification:
 *
 * <code>
 * $object = $dic->getInstance('\Carrot\Core\DependencyInjectionContainer@main');
 * </code>
 *
 * You have to use a fully qualified class name plus a configuration name separated
 * by '@' sign as the registration ID. This way you can have different anonymous functions
 * registered on the same class:
 *
 * <code>
 * \Namespace\Subnamespace\ClassName@configuration_name
 * \Carrot\Library\MySQLDatabase@primary_database
 * \Carrot\Library\MySQLDatabase@backup_database
 * </code>
 *
 * Please note that this class doesn't have the responsibility to require the class
 * files, it is the responsibility of the autoloader to do so.
 *
 * Dependency registrations are done in dependency registration files, which are assigned
 * to either a fully qualified namespace or the fully qualified class name. This means
 * you can assign registration files to: '\Namespace' or '\Namespace\Subnamespace\ClassName':
 *
 * <code>
 * $registrations['\Namespace'] = 'abs/path/to/conf-b.php';
 * $registrations['\Namespace\Subnamespace'] = 'abs/path/to/conf-a.php';
 * </code>
 *
 * When this class is used to get an instance of '\Namespace\Subnamespace\ClassName@main',
 * it will first search for an existing registration by that ID. If it doesn't exist,
 * this class will look (and load) dependency registration files assigned to (in order):
 *
 * <code>
 * \Namespace
 * \Namespace\Subnamespace
 * \Namespace\Subnamespace\ClassName
 * </code>
 * 
 * If after loading a registration file the item ID exists, it will stop loading files
 * immediately. If after loading all of the possible files the configuration item still
 * doesn't exists, it will try to instantiate the class without any construction arguments.
 * This will throw a warning if your class needs construction parameter(s).
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

namespace Carrot\Core;

class DependencyInjectionContainer
{
    /**
     * @var array List of loaded dependency registration file IDs.
     */
    protected $dependency_registration_files_loaded = array();
    
    /**
     * @var type List of dependency registration file paths, indexed by their assignment IDs.
     */
    protected $dependency_registration_files = array();
    
    /**
     * @var array List of formatted configuration items indexed by their registration ID.
     */
    protected $items = array();
    
    /**
     * @var array List of shared objects indexed by their registration ID.
     */
    protected $shared = array();
    
    /**
     * Constructs a DIC object.
     * 
     * @param array $dependency_registration_files List of dependency registration file paths indexed by their assignment IDs.
     *
     */
    public function __construct(array $dependency_registration_files)
    {
        $this->dependency_registration_files = $dependency_registration_files;
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
     *    $request = $dic->getInstance('\Carrot\Core\Request@shared');
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
     * @param string $dic_id DIC item registration ID.
     * @param Closure $function Anonymous function that returns the instantiated object.
     * 
     */
    public function register($dic_id, \Closure $function)
    {
        if (isset($this->items[$dic_id]))
        {
            throw new \InvalidArgumentException("Error in registering DIC item, ID '{$dic_id}' already exists.");
        }
        
        if (!$this->validateID($dic_id))
        {
            throw new \InvalidArgumentException("Error in registering DIC item, '{$dic_id}' is not a valid DIC registration ID.");
        }
        
        $this->items[$dic_id]['function'] = $function;
        $this->items[$dic_id]['class_name'] = $this->getClassNameFromID($dic_id);
    }
    
    /**
     * Saves an instance to the cache.
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
     * @param string $dic_id DIC item registration ID.
     * @param mixed $object The object reference to be saved.
     *
     */
    public function saveShared($dic_id, $object)
    {   
        if (!isset($this->items[$dic_id]))
        {
            throw new \InvalidArgumentException("Error in saving shared object reference, DIC item '{$dic_id}' doesn't exist.");
        }
        
        if (!is_object($object))
        {
            throw new \InvalidArgumentException("Error in saving shared object reference ({$dic_id}), expected object, '{$object}' given.");
        }
        
        // Validate class name
        $class_name_from_id = $this->getClassNameFromID($dic_id);
        $class_name_from_obj = '\\' . get_class($object);
        
        if ($class_name_from_id !== $class_name_from_obj)
        {
            throw new \InvalidArgumentException("Error in saving shared object reference ({$dic_id}), expected an instance of '{$class_name_from_id}', got '{$class_name_from_obj}' instead.");
        }
        
        $this->shared[$dic_id] = $object;
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
     * If the registration item does not exist, it will try to load registration
     * file assigned to it, from the top level namespace to the class name. For
     * example, when loading '\Carrot\Library\Config@main' it will try to find
     * the DIC item in dependency registration files assigned to (in order):
     *
     * <code>
     * \Carrot
     * \Carrot\Library
     * \Carrot\Library\Config
     * </code>
     *
     * If the item doesn't exist even after it has loaded all possible registration
     * files, it will try to instantiate the class without parameters.
     *
     * @id string $dic_id DIC item ID.
     * @return Object 
     *
     */
    public function getInstance($dic_id)
    {
        if (!$this->validateID($dic_id))
        {
            throw new \InvalidArgumentException("Error in getting instance, '{$dic_id}' is not a valid DIC item ID.");
        }
        
        // Return shared instance if it exists
        if (isset($this->shared[$dic_id]))
        {
            return $this->shared[$dic_id];
        }
        
        // Load dependency registration file for the package in question if we haven't already
        $this->loadDependencyRegistrationFile($dic_id);
        $class_name = $this->getClassNameFromID($dic_id);
        
        // If it doesn't exist, try to instantiate it without parameters
        if (!isset($this->items[$dic_id]))
        {
            if (!class_exists($class_name))
            {
                throw new \InvalidArgumentException("Error in getting instance, DIC item '{$dic_id}' does not exist, class name '{$class_name}' also doesn't exist.");
            }
            
            $object = new $class_name();
            return $object;
        }
        
        // Otherwise instantiate it using saved anonymous function
        $object = $this->items[$dic_id]['function']($this);
        $class_name_from_obj = '\\' . get_class($object);
        
        if ($class_name_from_obj !== $class_name)
        {
             throw new \RuntimeException("Error in getting instance, DIC item '{$dic_id}' does not return the correct instance, expecting an instance of '{$class_name}', got '{$class_name_from_obj}' instead.");
        }
        
        return $object;
    }
    
    // ---------------------------------------------------------------
    
    /**
     * Loads a dependency registration file for a particular ID.
     *
     * Does not load registration file if it has been loaded already, will mark a
     * registration file ID as loaded even if the file doesn't exist.
     * 
     * @param string $dic_id
     *
     */
    protected function loadDependencyRegistrationFile($dic_id)
    {  
        if (array_key_exists($dic_id, $this->items))
        {
            return;
        }
        
        $registration_file_assignment_id = '';
        $class_name_exploded = explode('\\', $this->getClassNameFromID($dic_id));
        
        foreach ($class_name_exploded as $segment)
        {
            if (empty($segment))
            {
                continue;
            }
            
            $registration_file_assignment_id .= "\\{$segment}";
            
            // Don't load if already loaded
            if (in_array($registration_file_assignment_id, $this->dependency_registration_files_loaded))
            {
                continue;
            }
            
            // Mark the registration file ID as loaded
            $this->dependency_registration_files_loaded[] = $registration_file_assignment_id;
            
            // Load the file if exists
            if (isset($this->dependency_registration_files[$registration_file_assignment_id]))
            {
                $this->requireDependencyRegistrationFile($this->dependency_registration_files[$registration_file_assignment_id]);   
                
                if (array_key_exists($dic_id, $this->items))
                {
                    break;
                }
            }
        }
    }
    
    /**
     * Actually requires() the given file path.
     *
     * This function is needed in order give the required dependency registration
     * file paths a clean variable scope.
     *
     * @param string $file_path Absolute path to the dependency registration file.
     *
     */
    protected function requireDependencyRegistrationFile($file_path)
    {   
        if (file_exists($file_path))
        {
            $require = function($dic, $file_path)
            {
                require_once($file_path);
            };
            
            $require($this, $file_path);
        }
    }
    
    /**
     * Validates DIC registration ID.
     *
     * The following rules must be satisfied:
     *
     *  1. Must use fully qualifed name (with starting backslash).
     *  2. Must have a configuration name after FQN, separated by '@'.
     *
     * @param string $dic_id DIC item ID.
     * @return bool TRUE if valid, FALSE otherwise.
     *
     */
    protected function validateID($dic_id)
    {
        $dic_id_exploded = explode('@', $dic_id);
        
        return
        (
            count($dic_id_exploded) == 2 &&
            !empty($dic_id_exploded[0]) &&
            !empty($dic_id_exploded[1]) &&
            $dic_id_exploded[0]{0} == '\\'
        );
    }
    
    /**
     * Get the fully qualified class name from DIC item ID.
     *
     * @param string $dic_id DIC item ID.
     * @return string Fully qualified class name.
     *
     */
    protected function getClassNameFromID($dic_id)
    {
        $dic_id_exploded = explode('@', $dic_id);
        return $dic_id_exploded[0];
    }
}