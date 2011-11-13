<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Autoloader
 * 
 * Used to set autoloading behavior. Adheres to PSR-0 final
 * proposal specifications. Configure the autoloader by binding a
 * namespace with a particular directory:
 *
 * <code>
 * $autoloader = new PSR0Autoloader;
 * $autoloader->bindNamespace(
 *     'Carrot\Core',
 *     '/absolute/path/to/directory'
 * );
 * </code>
 *
 * Autoloader will then look for class files for namespaces
 * Carrot\Core inside that folder according to PSR-0 specs.
 *
 * You can bind the root namespace to have every class looked for
 * in that directory:
 *
 * <code>
 * $autoloader->bindNamespace('\\', '/vendors');
 * </code>
 *
 * If more than one directory is bound to the same namespace, the
 * first file found will be loaded and Autoloader will stop
 * searching. Autoloader searches the file from the first
 * binding to the last.
 *
 * <code>
 * $autoloader->bindNamespace('\\', '/vendors');
 * $autoloader->bindNamespace('\\', '/providers');
 * </code>
 *
 * For the example code above, the Autoloader will first look for
 * the class file in '/vendors', if none found, '/providers' will
 * be searched.
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Autoloader;

use InvalidArgumentException;

class PSR0Autoloader implements AutoloaderInterface
{
    /**
     * @var array Contains namespace to directory bindings. The path
     *      to directory saved with a trailing directory separator.
     *      The namespace saved without backslash prefix, but with a
     *      backslash suffix.
     */
    protected $namespaceBindings = array();
    
    /**
     * @var array Contains class to file bindings, class name saved
     *      without backslash prefix.
     */
    protected $classBindings = array();
    
    /**
     * @var bool TRUE if this class has already registered its method
     *      using spl_autoload_register().
     */
    protected $registered = FALSE;
    
    /**
     * @var array List of files that this autoloader loaded, for
     *      debugging purposes.
     */
    protected $loadedFiles = array();
    
    /**
     * Bind namespace to a directory.
     *
     * This method tells the autoloader to look for files for classes
     * belonging to the given namespace at the given directory,
     * according to PSR-0 specifications.
     *
     * <code>
     * $autoloader->bindNamespace(
     *     'Carrot\Core',
     *     '/vendors/Carrot/Core/'
     * );
     * </code>
     *
     * With the above call, the autoloader will translate as such:
     *
     * <code>
     * Carrot\Core\Application -> /vendors/Carrot/Core/Application.php
     * Carrot\Request\Request -> /vendors/Carrot/Request/Request.php
     * </code>
     *
     * To bind a generic PSR-0 class file directory, simply bind the
     * root namespace:
     *
     * <code>
     * $autoloader->bindNamespace('\\', '/vendors');
     * </code>
     *
     * @param string $namespace Fully qualified namespace.
     * @param string $directory Absolute path to the directory.
     *
     */
    public function bindNamespace($namespace, $directory)
    {
        $namespace = trim($namespace, '\\');
        $namespace .= '\\';
        $directory = $this->validateDirectory($directory);
        $this->namespaceBindings[] = array(
            'namespace' => $namespace,
            'directory' => $directory
        );
    }
    
    /**
     * Bind class to an absolute file path.
     *
     * Class to file file bindings has higher priority than namespace
     * bindings, and thus is checked first.
     *
     * <code>
     * $autoloader->bindClass('Acme\App\BlogController', '/path/to/BlogController.php');
     * </code>
     * 
     * @param string $className Fully qualified class name.
     * @param string $absoluteFilePath Absolute file path to the class file.
     *
     */
    public function bindClass($className, $absoluteFilePath)
    {
        $className = ltrim($className, '\\');
        $this->classBindings[$className] = $absoluteFilePath;
    }
    
    /**
     * Registers {@see loadClass()} method using
     * spl_autoload_register().
     *
     * @see loadClass()
     *
     */
    public function register()
    {
        if (!$this->registered)
        {
            spl_autoload_register(array($this, 'loadClass'));
            $this->registered = TRUE;
        }
    }
    
    /**
     * Unregisters {@see loadClass()} method using
     * spl_autoload_unregister().
     *
     * @see loadClass()
     *
     */
    public function unregister()
    {
        if ($this->registered)
        {
            spl_autoload_unregister(array($this, 'loadClass'));
            $this->registered = FALSE;
        }
    }
    
    /**
     * Loads the class file.
     *
     * First tries to load the class file from class to file
     * bindings. If it fails, tries to load the file from namespace
     * to directory bindings.
     * 
     * @see loadFromClassBindings()
     * @see loadFromNamespaceBindings()
     * @param string $className Fully qualified class name.
     *
     */
    public function loadClass($className)
    {   
        $className = ltrim($className, '\\');
        
        if ($this->loadFromClassBindings($className))
        {
            return;
        }
        
        $this->loadFromNamespaceBindings($className);
    }
    
    /**
     * Gets the list of loaded files.
     *
     * {@see AutoloaderInterface::getLoadedFiles()} for an example of
     * returned array structure.
     *
     * @see AutoloaderInterface::getLoadedFiles()
     * @return array
     *
     */
    public function getLoadedFiles()
    {
        return $this->loadedFiles;
    }
    
    /**
     * Validates a directory with realpath().
     *
     * Adds trailing directory separator to the directory.
     *
     * Throws InvalidArgumentException if the string is not a valid
     * path or the directory doesn't exists.
     *
     * @throws InvalidArgumentException
     * @param string $directory The directory string to validate.
     * @return string Path to the directory with trailing directory separator.
     *
     */
    protected function validateDirectory($directory)
    {
        $formattedDirectory = realpath($directory);
        
        if ($formattedDirectory === FALSE)
        {
            throw new InvalidArgumentException("PSR0Autoloader error in binding namespace, either '{$directory}' is not a valid path or the directory doesn't exist.");
        }
        
        if (substr($formattedDirectory, -1) != DIRECTORY_SEPARATOR)
        {
            $formattedDirectory .= DIRECTORY_SEPARATOR;
        }
        
        return $formattedDirectory;
    }
    
    /**
     * Load the class file from class to file bindings.
     *
     * Throws RuntimeException if a class to file binding is found but
     * the class file doesn't exist.
     *
     * @throws RuntimeException
     * @param string $className Fully qualified class name, without backslash prefix.
     * @return bool TRUE if class file found and loaded, FALSE otherwise.
     *
     */
    protected function loadFromClassBindings($className)
    {
        if (array_key_exists($className, $this->classBindings))
        {
            if (!file_exists($this->classBindings[$className]))
            {
                throw new \RuntimeException("PSR0Autoloader error, unable to load '{$className}' class, the file '{$this->classBindings[$className]}' does not exist.");
            }
            
            require $this->classBindings[$className];
            $this->logLoadedFile($className, $this->classBindings[$className]);
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * Loads the class from namespace to directory bindings.
     *
     * Checks the namespace bindings one by one. If the class name
     * contains the namespace string, generate a path for the
     * binding.
     * 
     * @param string $className Fully qualified class name without backslash prefix.
     * @return bool TRUE if class file found and loaded, FALSE otherwise.
     *
     */
    protected function loadFromNamespaceBindings($className)
    {
        foreach ($this->namespaceBindings as $binding)
        {
            $rootNamespace = $binding['namespace'];
            $rootDirectory = $binding['directory'];
            
            if (stripos($className, $rootNamespace) === 0 OR $rootNamespace == '\\')
            {                
                $classNameSnippet = substr($className, strlen($rootNamespace));
                
                if ($rootNamespace == '\\')
                {
                    $classNameSnippet = $className;
                }
                
                $filePath = $this->convertToFilePath($classNameSnippet, $rootDirectory);
                
                if (file_exists($filePath))
                {
                    require $filePath;
                    $this->logLoadedFile($className, $filePath);
                    return TRUE;
                }
            }
        }
        
        return FALSE;
    }
    
    /**
     * Converts a class name snippet into a file path segment, based
     * on PSR-0 rules.
     *
     * This method will convert 'Carrot\Core\Class_Name' to a
     * relative path (Carrot/Core/Class/Name.php) according to the
     * PSR-0 specification.
     *
     * @param string $className Class name to convert (with namespace), without backslash prefix and suffix
     * @param string $rootDirectory The root directory, with trailing directory separator.
     * @return string Path to the class file.
     *
     */
    protected function convertToFilePath($className, $rootDirectory)
    {
        $classNamespace = $this->extractNamespace($className);
        $classNameWithoutNamespace = $this->removeNamespace($className);
        return
            $rootDirectory .
            str_replace('\\', DIRECTORY_SEPARATOR, $classNamespace) . DIRECTORY_SEPARATOR .
            str_replace('_', DIRECTORY_SEPARATOR, $classNameWithoutNamespace) . '.php';
    }
    
    /**
     * Extract the namespace from a class name.
     * 
     * If the namespace is root, it returns an empty string.
     * 
     * <code>
     * Carrot\Core\Application -> 'Carrot\Core'
     * App\BlogController -> 'App'
     * Autoloader -> ''
     * </code>
     *
     * @param string $className Class name with namespace (without backslash prefix).
     * @return string The namespace of the class.
     *
     */
    protected function extractNamespace($className)
    {
        if ($lastNamespaceSeparatorPosition = strripos($className, '\\'))
        {
            return substr($className, 0, $lastNamespaceSeparatorPosition);
        }
        
        return '';
    }
    
    /**
     * Remove namespace from a class name.
     *
     * <code>
     * Carrot\Core\Autoloader -> 'Autoloader'
     * App\BlogController -> 'BlogController'
     * Autoloader -> 'Autoloader'
     * </code>
     *
     * @param string $className Class name with namespace (without backslash prefix).
     * @return string Class name without namespaces (no backslashes).
     *
     */
    protected function removeNamespace($className)
    {
        if ($lastNamespaceSeparatorPosition = strripos($className, '\\'))
        {
            return substr($className, $lastNamespaceSeparatorPosition + 1);
        }
        
        return $className;
    }
    
    /**
     * Logs the loaded class file for debug information.
     *
     * @see getLoadedFiles()
     * @param string $className Fully qualified class name.
     * @param string $filePath Absolute path to the class file loaded.
     *
     */
    protected function logLoadedFile($className, $filePath)
    {   
        $this->loadedFiles[$className] = array(
            'time' => time(),
            'path' => $filePath
        );
    }
}