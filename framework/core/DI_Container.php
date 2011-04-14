<?php

/**
 * Dependency Injection Container.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package			Carrot
 * @author 		  	Ricky Christie <seven.rchristie@gmail.com>
 * @copyright		2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license			http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		 	0.1
 * @version			0.1
 */

/**
 * Dependency Injection Container
 *
 * Inspired by {@link https://github.com/gooh/SimpleDIC SimpleDIC} by Gordon Oheim.
 * This class will instantiate an object along with its dependencies recursively.
 * Not only instantiating the class, it will also try to search and require the
 * files, assuming that the name of the class is the same with the name of the
 * file. Instantiated objects are stored inside a protected property. Default
 * behavior is set to return references of objects already instantiated, although
 * you can also force the class to create a new instance.
 * 
 * @package			Carrot
 * @author 		  	Ricky Christie <seven.rchristie@gmail.com>
 * @copyright		2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license			http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		 	0.1
 * @version			0.1
 * @todo			Validate $transients & $singletons in constructor, both must not contain references to the same classes.
 */

class DI_Container
{
	/**
	 * @var array Cache of references to instantiated objects.
	 */
	protected $instances = array();
	
	/**
	 * @var array List of directories to look for class files. Used when trying to require file. (With trailing slash).
	 */
	protected $search_paths;
	
	/**
	 * @var string String that marks a parameter as a class name.
	 */
	protected $type_object = 'Object';
	
	/**
	 * @var string String that marks a parameter as a class name, to be instantiated even if we have a cache.
	 */
	protected $type_object_force = 'Object:force';
	
	/**
	 * @var string String that marks a parameter as an array, to be traversed and interpreted recursively.
	 */
	protected $type_array = 'Array';
	
	/**
	 * @var array Contains parameters used in instantiating objects.
	 */
	protected $config;
	
	/**
	 * @var array List of classes that must not be instantiated twice.
	 */
	protected $singletons;
	
	/**
	 * @var type comments
	 */
	protected $transients;
	
	/**
	 * @var array List of classes that must not be instantiated.
	 */
	protected $forbidden;
	
	/**
	 * Constructs the DI_Container.
	 *
	 * Search paths are array of paths that the class uses to
	 * look for the class file, as in:
	 *
	 * <code>
	 * $search_paths = array
	 * (
	 *    '/absolute/path/to/libraries',
	 *    '/absolute/path/to/controllers',
	 *    '/absolute/path/to/views'
	 * );
	 * </code>
	 *
	 * Configuration is the list of parameters to pass to construct
	 * a particular class. The array will be sorted using ksort()
	 * to determine the order, so beware the index.
	 *
	 * <code>
	 * $config['Class_name'] = array
	 * (
	 *    0 => array('Contents' => 'Some string value', 'Type' => 'Value'),
	 *    1 => array('Contents' => $object_ref, 'Type' => 'Value'),
	 *    2 => array('Contents' => array('pear', 'grape', 'lime', 'lemon'), 'Type' => 'Value'),
	 *    3 => array('Contents' => array
	 *                             (
	 *                                0 => array('Contents' => 9938, 'Type' => 'Value'),
	 *                                1 => array('Contents' => 'Class_name', 'Type' => 'Object')
	 *                             ), 'Type' => 'Array'),
	 *    4 => array('Contents' => 'Class_name', 'Type' => 'Object'),
	 *    5 => array('Contents' => 'Class_name', 'Type' => 'Object:force')
	 * );
	 * </code>
	 *
	 * There are three types of parameter types you can use:
	 * 
	 *  1. Object - means get an instance of this object, returns cache object
	 * 	   when available.
	 *  2. Object:force - means get an instance of this object, ignore cache and
	 *     instantiate a new object no matter what.
	 *  3. Array - contains an array of configuration, to be processed recursively.
	 *  4. Value - the contents are passed without any further processing.
	 *
	 * Class names inside $singletons would not be instantiated more than once. This
	 * is useful in the case of $config or $DB objects, where you want every other object
	 * to refer to the same object.
	 *
	 * Class names inside $forbidden will never be instantiated. If the user tries to
	 * instantiate it using get_instance(), this class will throw RuntimeException.
	 * 
	 * Class names listed in $transients will not be cached inside $this->instances. This
	 * means subsequent get_instance() call will always construct a new instance of this
	 * class.
	 *
	 * @param array $search_paths Arrays of paths, to look for class files.
	 * @param array $config List of constructor parameters for each class.
	 * @param array $forbidden List of class names that must not be instantiated.
	 * @param array $singletons List of class names that mustn't be instantiated more than once.
	 * @param array $transients List of class names whose references will not be stored.
	 *
	 */
	public function __construct(array $search_paths, array $config = array(), array $forbidden = array(), array $singletons = array(), array $transients = array())
	{	
		$this->search_paths = $search_paths;
		$this->config = $config;
		$this->forbidden = $forbidden;
		$this->singletons = $singletons;
		$this->transients = $transients;
	}
	
	/**
	 * Load an object instance reference.
	 *
	 * This class keeps references to instantiated objects in $this->instances,
	 * so that when an instance is needed, it can return a reference without 
	 * constructing a new one. We can use this method to populate $this->instances
	 * with references to objects not instantiated using this class.
	 *
	 * Default behavior is not to overwrite if a reference already exists.
	 * We can change this using optional $overwrite parameter. If the class_name
	 * of the object is included in transient.
	 *
	 * @param string $class_name
	 * @param string $object Must be an object.
	 * @param bool $overwrite Defaults to FALSE.
	 * @return bool TRUE on success, FALSE on failure.
	 *
	 */
	public function load_instance($class_name, $object, $overwrite = FALSE)
	{
		// Class name and object must match.
		if (is_object($object) && get_class($object) == $class_name)
		{
			// Transient classes should not be cached.
			if (in_array($class_name, $this->transients))
			{
				return FALSE;
			}
			
			if (isset($this->instances[$class_name]))
			{
				// If allowed to overwrite
				if ($overwrite)
				{
					$this->instances[$class_name] = $object;
					return TRUE;
				}
				
				return FALSE;
			}
			
			$this->instances[$class_name] = $object;
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Remove references to an object in instances.
	 * 
	 * Instantiated objects are kept as reference in $this->instances.
	 * By keeping references, we can cut processing time by returning
	 * a reference to a cached object when an instance is needed. However,
	 * this also means that some objects will live longer than necessary.
	 * Yu can use this method to remove the reference from $this->instance.
	 *
	 * @param string $class_name
	 *
	 */
	public function unload_instance($class_name)
	{
		unset($this->instances[$class_name]);
	}
	
	/**
	 * Gets an instance from the list. Instantiates new object if doesn't exist.
	 *
	 * This method allows the overloading of parameters. Details of overloading
	 * rules can be read at $this->overload_config(). Default behavior is to
	 * return cached objects. This can be changed by setting $force_instantiation
	 * to TRUE. Details of the behavior can be read at $this->is_allowed_to_use_cache().
	 *
	 * @see DI_Container::overload_config()
	 * @see DI_Container::is_allowed_to_use_cache()
	 * @see DI_Container::instantiate()
	 * @see DI_Container::prepare_ctor_arguments()
	 * @param string $class_name
	 * @param array $config Configuration overload.
	 * @return object
	 *
	 */
	public function get_instance($class_name, array $config = array(), $force_instantiation = FALSE)
	{			
		if (in_array($class_name, $this->forbidden))
		{
			throw new RuntimeException("DIC error in instantiating. Class {$class_name} is not allowed to be instantiated.");
		}
		
		if ($this->is_allowed_to_use_cache($class_name, $force_instantiation))
		{
			return $this->instances[$class_name];
		}
		
		// Otherwise instantiate the class
		return $this->instantiate($class_name, $config);
	}
	
	/**
	 * Requires the class file (if necessary).
	 *
	 * If the class doesn't exist yet, this method will search
	 * for the class file to load in $this->search_paths, assuming
	 * that the name of the file is the "{$class_name}.php". If
	 * at the end, the class is not found, it throws an exception.
	 *
	 * @param string $class_name Class name without markers.
	 * @throws RuntimeException
	 *
	 */
	public function require_file($class_name)
	{
		if (!class_exists($class_name))
		{
			// Iterate through search_paths to find the file.
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
			
			if (!$found)
			{
				throw new RuntimeException("DIC error in loading class. File '{$file_name}.php' not found in search paths.");
			}
			
			require($path);
			
			if (!class_exists($class_name))
			{
				throw new RuntimeException("DIC error in loading class. File ({$file_name}.php) does not contain the class {$class_name}.");
			}
		}
	}
	
	/**
	 * Registers new instantiation configuration at $this->config.
	 * 
	 * This class will instantiate objects by injecting parameters declared
	 * inside $this->config. This method allows the user to define new config
	 * after this class is already instantiated.
	 *
	 * Default behavior is to not overwrite configuration if it already
	 * exists. We can change this using the optional $overwrite parameter.
	 *
	 * @param string $class_name
	 * @param array $config The configuration array.
	 * @param bool $overwrite Optional, defaults to FALSE.
	 * @return bool TRUE on success, FALSE on failure.
	 * 
	 */
	public function register($class_name, $config, $overwrite = FALSE)
	{
		if (isset($this->config[$class_name]))
		{
			if ($overwrite)
			{
				$this->config[$class_name] = $config;
				return TRUE;
			}
			
			return FALSE;
		}
		
		$this->config[$class_name] = $config;
		return TRUE;
	}
	
	// ---------------------------------------------------------------
	
	/**
	 * Replace argument with default if it's empty.
	 *
	 * @param string $argument The argument to be checked for emptiness.
	 * @param string $default 
	 *
	 */
	protected function use_default_if_empty($argument, $default)
	{
		if (empty($argument))
		{
			return $default;
		}
		
		return $argument;
	}
	
	/**
	 * Returns TRUE if allowed to return cache.
	 *
	 * Checks if we have a cache or not. Returns FALSE if we don't
	 * have a cache to return. Also returns FALSE if $class_name is
	 * on $this->transients list. Returns TRUE when we have a cache and
	 * $class_name is part of $this->singletons. Returns TRUE when we
	 * have a cache, $class_name does not exist in $this->singletons but
	 * $force_instantiation is false.
	 *
	 * Listed in $this->transients will trumps everything. Listed in
	 * $this->skeletons will trumps $force_instantiation flag. Default
	 * behavior is to allow usage of cache.
	 *
	 * @param string $class_name
	 * @param bool $force_instantiation
	 * @return bool TRUE if allowed, FALSE if otherwise.
	 *
	 */
	protected function is_allowed_to_use_cache($class_name, $force_instantiation)
	{
		return (array_key_exists($class_name, $this->instances) && (in_array($class_name, $this->singletons) or !$force_instantiation) && !in_array($class_name, $this->transients));
	}
	
	/**
	 * Instantiate a class.
	 *
	 * If config is empty or doesn't exist, it will instantiate the object
	 * without construction parameters. Calls $this->prepare_ctor_arguments()
	 * to convert configuration into an array of parameters. It handles overloading
	 * of configuration using $this->overload_config().
	 *
	 * @see DI_Container::overload_config()
	 * @see DI_Container::prepare_ctor_arguments()
	 * @param string $class_name Class name without markers.
	 * @param string $config Config overloading, will be merged with $this->config[$class_name].
	 * @return object Instantiated object.
	 *
	 */
	protected function instantiate($class_name, array $config)
	{
		$this->require_file($class_name);
		
		if (!isset($this->config[$class_name]))
		{
			$this->config[$class_name] = array();
		}
		
		$config = $this->overload_config($this->config[$class_name], $config);
		$ctor_arguments = $this->prepare_ctor_arguments($config);
		
		if (empty($ctor_arguments))
		{
			$object = new $class_name();
		}
		else
		{
			$reflection = new ReflectionClass($class_name);
			$object = $reflection->newInstanceArgs($ctor_arguments);	
		}
		
		if (!in_array($class_name, $this->transients))
		{
			$this->instances[$class_name] = $object;
		}
		
		return $object;
	}
	
	/**
	 * Overload $base_config with $config.
	 *
	 * @param array $base_config Base configuration array.
	 * @param array $config_overload Configuration array to overload $base_config.
	 * @return array Merged configuration array.
	 *
	 */
	protected function overload_config(array $base_config, array $config_overload)
	{
		foreach ($config_overload as $index => $content)
		{
			$base_config[$index] = $content;
		}
		
		ksort($base_config);
		return $base_config;
	}
	
	/**
	 * Prepares constructor arguments by parsing configuration variable.
	 *
	 * Depending on the type, it will either run $this->get_instance() or
	 * call this method to prepare constructor arguments recursively. It
	 * will ignore a config item if it does not have the index 'Type' or
	 * 'Content'.
	 *
	 * @param array $config Configuration to parse.
	 * @return array Constructor arguments.
	 *
	 */
	protected function prepare_ctor_arguments(array $config)
	{	
		$ctor_arguments = array();
		
		foreach ($config as $item)
		{	
			if (!is_array($item) or !isset($item['Type']) or !isset($item['Contents']))
			{
				continue;
			}
			
			switch ($item['Type'])
			{
				case $this->type_object:
					$ctor_arguments[] = $this->get_instance($item['Contents']);
				break;
				case $this->type_object_force:
					$ctor_arguments[] = $this->get_instance($item['Contents'], array(), TRUE);
				break;
				case $this->type_array:
					$ctor_arguments[] = $this->prepare_ctor_arguments($item['Contents']);
				break;
				default:
					$ctor_arguments[] = $item['Contents'];
				break;
			}
		}
		
		return $ctor_arguments;
	}
}