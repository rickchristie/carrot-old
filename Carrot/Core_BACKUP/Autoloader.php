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
 * $autoloader = new Autoloader;
 * $autoloader->bindNamespaceToDirectory('Carrot\Core', '/absolute/path/to/folder');
 * </code>
 *
 * Autoloader will then look for class files for namespaces
 * Carrot\Core inside that folder according to PSR-0 specs.
 *
 * You can bind the root namespace to have every class looked for
 * in that directory:
 *
 * <code>
 * $autoloader->bindNamespaceToDirectory('\\', '/vendors');
 * </code>
 *
 * If more than one directory is bound to the same namespace, the
 * first file found will be loaded and Autoloader will stop
 * searching. Autoloader searches the file from the first
 * binding to the last.
 *
 * <code>
 * $autoloader->bindNamespaceToDirectory('\\', '/vendors');
 * $autoloader->bindNamespaceToDirectory('\\', '/providers');
 * </code>
 *
 * For the example code above, the Autoloader will first look for
 * the class file in '/vendors', if none found, '/providers' will
 * be searched.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class Autoloader
{
    /**
     * @var array Contains namespace to directory bindings, the directory saved with a trailing directory separator, namespace with backslash prefix.
     */
    protected $namespaceToDirectoryBindings = array();
    
    /**
     * @var array Contains class to absolute file path bindings, class name saved with backslash prefix.
     */
    protected $classToFileBindings = array();
    
    /**
     * @var bool TRUE if this class has already registered its method using spl_autoload_register.
     */
    protected $registered = FALSE;
    
    /**
     * @var array List of files that this autoloader really loads, for debugging purposes.
     */
    protected $loadedFiles = array();
    
    /**
     * Bind a namespace to a particular directory.
     *
     * Once you've bound a namespace to a directory, the autoloader
     * will try to find class files inside that directory, according
     * to PSR-0 specifications.
     *
     * <code>
     * $autoloader->bindNamespaceToDirectory('Carrot\Core', '/vendors/Carrot/Core/');
     * </code>
     *
     * After the above binding, the autoloader will try to load:
     *
     * <code>
     * Carrot\Core\DependencyInjectionContainer -> /vendors/Carrot/Core/DependencyInjectionContainer.php
     * Carrot\Core\Interfaces\ProviderInterface -> /vendors/Carrot/Core/Interfaces/ProviderInterface.php
     * </code>
     *
     * To bind a generic PSR-0 class file directory, simply bind the
     * root namespace ('/') to a directory.
     * 
     * @param string $namespace Fully qualified namespace.
     * @param string $pathToDirectory Absolute directory path.
     * 
     */
    public function bindNamespaceToDirectory($namespace, $pathToDirectory)
    {
        $namespace = rtrim($namespace, '\\');
        $namespace = $this->addBackslashPrefix($namespace);
        $pathToDirectory = $this->validateDirectory($pathToDirectory);
        $this->namespaceToDirectoryBindings[] = array('namespace' => $namespace, 'directory' => $pathToDirectory);
    }
    
    /**
     * Bind a fully qualified class name to an absolute file path.
     * 
     * You can bind a class to a particular class file. This type of
     * binding gets a priority.
     * 
     * <code>
     * $autoloader->bindClassToFile('Acme\App\BlogController', '/path/to/BlogController.php');
     * </code>
     * 
     * @param string $className Fully qualified class name.
     * @param string $absoluteFilePath Absolute file path to the class file.
     * 
     */
    public function bindClassToFile($className, $absoluteFilePath)
    {
        $className = rtrim($namespace, '\\');
        $className = $this->addBackslashPrefix($className);
        $this->classToFileBindings[$className] = $absoluteFilePath;
    }
    
    /**
     * Registers $this->loadClass() method using spl_autoload_register().
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
     * Unregisters $this->loadClass() method using spl_autoload_unregister().
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
     * This method will first look for the class to file bindings. If
     * none are found, the class that checks the namespace to
     * directory bindings for the class file. If the file is found
     * this method returns immediately.
     *
     * This method checks sequentially. If there is more than one
     * class file in namespace to directory bindings, it will load
     * the first file it found and not look for the rest.
     *
     * @param string $className Class name to load
     *
     */
    public function loadClass($className)
    {
        $className = $this->addBackslashPrefix($className);
        
        if ($this->loadClassFromClassToFileBindings($className))
        {
            return;
        }
        
        $this->loadClassFromNamespaceToDirectoryBindings($className);
    }
    
    /**
     * Adds a backslash prefix to a string.
     *
     * Changes 'Carrot\Core' to '\Carrot\Core'.
     * 
     * Used to turn all namespaces or class names to a fully qualified
     * namespace. This makes sure the namespace is always traversing
     * from root, which makes it consistent.
     *
     * Does not add a backslash prefix if the string already has a
     * backslash prefix.
     *
     * @param string $string
     * @return string The string with backslash prefix.
     *
     */
    protected function addBackslashPrefix($string)
    {
        if (empty($string) or $string{0} != '\\')
        {
            $string = '\\' . $string;
        }
        
        return $string;
    }
    
    /**
     * Validates a directory with realpath() and is_dir().
     *
     * Adds trailing directory separator to the directory.
     *
     * Throws InvalidArgumentException if the string is not a valid
     * directory or the directory doesn't exists.
     *
     * @throws \InvalidArgumentException
     * @param string $pathToDirectory
     * @return string Path to the directory with trailing directory separator.
     *
     */
    protected function validateDirectory($pathToDirectory)
    {
        if (!is_dir($pathToDirectory))
        {
            throw new \InvalidArgumentException("Autoloader error in adding a vendor directory, '{$pathToDirectory}' is not a directory or it doesn't exist.");
        }
        
        $pathToDirectory = realpath($pathToDirectory);   
        
        if (substr($pathToDirectory, -1) != DIRECTORY_SEPARATOR)
        {
            $pathToDirectory .= DIRECTORY_SEPARATOR;
        }
        
        return $pathToDirectory;
    }
    
    /**
     * Loads the class from class to file bindings.
     *
     * Throws RuntimeException if a class to file binding is found but
     * the class file doesn't exist.
     * 
     * @throws \RuntimeException
     * @param string $className Fully qualified class name with backslash prefix.
     * @return bool TRUE if class file found and loaded, FALSE otherwise.
     *
     */
    protected function loadClassFromClassToFileBindings($className)
    {
        if (array_key_exists($className, $this->classToFileBindings))
        {
            if (!file_exists($this->classToFileBindings[$className]))
            {
                throw new \RuntimeException("Autoloader error, unable to load '{$className}' class, the file '{$this->classToFileBindings[$className]}' doesn't exist.");
            }
            
            require $this->classToFileBindings[$className];
            $this->loadedFiles[$className] = $this->classToFileBindings[$className];
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * Loads the class from namespace to directory bindings.
     * 
     * @param string $className Fully qualified class name with backslash prefix.
     * @return bool TRUE if class file found and loaded, FALSE otherwise.
     *
     */
    protected function loadClassFromNamespaceToDirectoryBindings($className)
    {   
        foreach ($this->namespaceToDirectoryBindings as $binding)
        {
            $namespaceBound = $binding['namespace'];
            $directoryBound = $binding['directory'];
            
            if (stripos($className, $namespaceBound) === 0)
            {   
                $classFilePath = $this->determineFilePath(substr($className, strlen($namespaceBound)), $directoryBound);
                
                if (file_exists($classFilePath))
                {
                    require $classFilePath;
                    $this->loadedFiles[$className] = $classFilePath;
                    return TRUE;
                }
            }
        }
        
        return FALSE;
    }
    
    /**
     * Turns namespaces and class name into a file path according to PSR-0 spec.
     *
     * This method will convert 'Carrot\Core\Class_Name' to a
     * relative path (Carrot/Core/Class/Name.php) according to the
     * PSR-0 specification.
     *
     * @param string $className Class name with namespaces.
     * @param string $rootDirectory Root directory, with trailing directory separator.
     * @return string Relative path to the class file.
     *
     */
    protected function determineFilePath($className, $rootDirectory)
    {
        $className = ltrim($className, '\\');
        $namespace = $this->extractNamespace($className);
        $className = $this->extractClassNameWithoutNamespaces($className);
        return $rootDirectory . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    }
    
    /**
     * Extract the namespace from a fully qualified class name.
     *
     * If the namespace is root, it returns an empty string.
     *
     * <code>
     * Carrot\Core\Autoloader -> 'Carrot\Core'
     * App\BlogController -> 'App'
     * Autoloader -> ''
     * </code>
     * 
     * @param string $className Fully qualified class name (without backslash prefix).
     * @return string Fully qualified namespace.
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
     * Extract the class name without namespaces.
     * 
     * <code>
     * Carrot\Core\Autoloader -> 'Autoloader'
     * App\BlogController -> 'BlogController'
     * Autoloader -> 'Autoloader'
     * </code>
     *
     * @param string $className Fully qualified class name (without backslash prefix).
     * @return string Class name without namespaces (no backslashes).
     *
     */
    protected function extractClassNameWithoutNamespaces($className)
    {
        if ($lastNamespaceSeparatorPosition = strripos($className, '\\'))
        {
            return substr($className, $lastNamespaceSeparatorPosition + 1);
        }
        
        return $className;
    }
}