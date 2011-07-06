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
 * Carrot's dependency injection container (DIC) creates object
 * graph with provider classes, similar to provider bindings in
 * Google Guice.
 * 
 * The provider class must implement
 * Carrot\Core\Interfaces\ProviderInterface. You can extend
 * Carrot\Core\Provider instead to avoid re-implementing methods
 * for each provider classes.
 * 
 * As the default behavior, this class will look for provider
 * class inside the same namespace as the class being provided,
 * with the same class name with added 'Provider' suffix. This
 * means that it will look for Vendor\Namespace\ClassNameProvider
 * when it needs to construct Vendor\Namespace\ClassName.
 *
 * This behavior can be overridden by binding another provider
 * class:
 *
 * <code>
 * $dic->bindProviderClass('App\CustomProviderClassName', 'Carrot\Core\FrontController');
 * </code>
 *
 * Each instantiation configuration is identified by an instance
 * name, which consists of the fully qualified class name and the
 * configuration name, separated by '@' character:
 *
 * <code>
 * Carrot\Database\MySQLi@Main
 * Carrot\Database\MySQLi@Logging
 * Carrot\Helper\Config@Shared
 * </code>
 *
 * When specifying the instance you want in the provider class,
 * you will have to provide the full instance name. The DIC will
 * call the appropriate method based on the configuration name:
 *
 * <code>
 * Carrot\Database\MySQLi@Main -> Carrot\Database\MySQLiProvider::getMain()
 * Carrot\Database\MySQLi@Logging -> Carrot\Database\MySQLiProvider::getLogging()
 * Carrot\Helper\Config@Shared -> Carrot\Helper\ConfigProvider::getShared()
 * </code>
 *
 * For more information, please see the docs for
 * {@see Carrot\Core\Interfaces\ProviderInterface} and
 * {@see Carrot\Core\Provider}.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use RuntimeException;

class DependencyInjectionContainer
{
    /**
     * @var array Contains provider to class bindings.
     */
    protected $providerToClassBindings = array();
    
    /**
     * @var array Contains provider to instance name bindings.
     */
    protected $providerToInstanceNameBindings = array();
    
    /**
     * @var string Suffix to the provider class name, added after the class name being provided.
     */
    protected $providerClassSuffix = 'Provider';
    
    /**
     * @var string Prefix added to the configuration name as the method to be called.
     */
    protected $providerMethodPrefix = 'get';
    
    /**
     * @var array Contains references to objects that has singleton lifecycle.
     */
    protected $singletons = array();
    
    /**
     * Loads a file that contains provider bindings.
     * 
     * In the file, you can use $dic variable to define provider class
     * bindings:
     *
     * <code>
     * $dic->bindProviderClass('App\Providers\ConfigProvider', 'Carrot\Helpers\Config');
     * </code>
     *
     * If you need to do some logic, don't forget that you can use the
     * $dic to get an instance of the classes you need.
     * 
     * @throws \InvalidArgumentException
     * @param string $providerFilePath Absolute file path to the provider configuration file.
     * @param bool $mustExist If true, will throw exception if the file doesn't exist. Defaults to true.
     *
     */
    public function loadProviderFile($providerFilePath, $mustExist = true)
    {
        if (!file_exists($providerFilePath) && $mustExist)
        {
            throw new \InvalidArgumentException("DIC error, cannot load provider file '{$providerFilePath}', file does not exist.");
        }
        
        $require = function($providerFilePath, $dic)
        {
            require $providerFilePath;
        };
        
        $require($providerFilePath, $this);
    }
    
    /**
     * Binds a provider class to a specific instance name.
     * 
     * Binding provider class to an instance name means that no matter
     * what, if the instance name is called, the provider class bound
     * to it will be the one used.
     *
     * <code>
     * $dic->bindProviderToInstanceName('App\Providers\ConfigProvider', 'Carrot\Helpers\Config@Main');
     * </code>
     *
     * This supersedes everything, including provider to class
     * bindings. You can't bind an instance name to more than one
     * provider class.
     *
     * @param string $providerClassName Fully qualified class name for the provider class.
     * @param string $instanceName The instance name to be bound to the provider class.
     *
     */
    public function bindProviderToInstanceName($providerClassName, $instanceName)
    {
        $instanceName = ltrim($instanceName, '\\');
        $providerClassName = ltrim($providerClassName, '\\');
        $this->providerToInstanceNameBindings[$instanceName] = $providerClassName;
    }
    
    /**
     * Adds a provider to instance name bindings array.
     *
     * If you need to do a lot of binding, you can define them in an
     * array first to reduce the total number of method calls.
     *
     * <code>
     * $bindings = array
     * (
     *     'App\Providers\ConfigProvider' => 'Carrot\Helpers\Config@Main',
     *     'App\Providers\MySQLiProvider' => 'Carrot\Database\MySQLi@BackupDB',
     *     'App\Providers\FrontControllerProvider' => 'Carrot\Core\FrontController@Main',
     *     ...
     * );
     *
     * $this->addProviderToInstanceNameBindings($bindings);
     * </code>
     *
     * @param array $bindings The bindings inside an array.
     *
     */
    public function addProviderToInstanceNameBindings(array $bindings)
    {
        foreach ($bindings as $providerClassName => $instanceName)
        {
            $instanceName = ltrim($instanceName, '\\');
            $providerClassName = ltrim($providerClassName, '\\');
            $this->providerToInstanceNameBindings[$instanceName] = $providerClassName;
        }
    }
    
    /**
     * Binds a provider class to a fully qualified class name.
     * 
     * Direct provider class bindings overrides the default provider
     * class searching behavior.
     *
     * <code>
     * $dic->bindProviderToClass('App\Providers\ConfigProvider', 'Carrot\Helpers\Config');
     * </code>
     * 
     * @param string $providerClassName Fully qualified class name for the provider class.
     * @param string $className Fully qualified class name for the class being provided.
     *
     */
    public function bindProviderToClass($providerClassName, $className)
    {
        $className = ltrim($className, '\\');
        $providerClassName = ltrim($providerClassName, '\\');        
        $this->providerToClassBindings[$className] = $providerClassName;
    }
    
    /**
     * Adds a provider class binding array.
     * 
     * If you need to do a lot of binding, using this method you can
     * just pass an array to avoid having to call bindProviderClass()
     * multiple times.
     *
     * <code>
     * $bindings = array
     * (
     *     'App\Providers\ConfigProvider' => 'Carrot\Helpers\Config',
     *     'App\Providers\MySQLiProvider' => 'Carrot\Database\MySQLi',
     *     'App\Providers\FrontControllerProvider' => 'Carrot\Core\FrontController',
     *     ...
     * );
     *
     * $this->addProviderToClassBindings($bindings);
     * </code>
     *
     * @param array $bindings Array containing the bindings.
     *
     */
    public function addProviderToClassBindings(array $bindings)
    {
        foreach ($bindings as $providerClassName => $className)
        {
            $providerClassName = ltrim($providerClassName, '\\');
            $className = ltrim($className, '\\');
            $this->providerToClassBindings[$className] = $providerClassName;
        }
    }
    
    /**
     * Gets an instance from the provider.
     * 
     * This method will find and instantiate the provider class. It
     * will provide the dependencies to the provider recursively by
     * calling ProviderInterface::getDependencies() and
     * ProviderInterface::setDependencies(). The appropriate provider
     * method is then called to return the instance needed.
     *
     * This method also calls ProviderInterface::isSingleton() to find
     * out if the object being instantiated has a singleton lifecycle.
     * It saves the object reference into a cache if it is indeed the
     * case.
     *
     * <code>
     * $mysqli = $dic->getInstance('Carrot\Database\MySQLi@Main');
     * </code>
     * 
     * @param string $instanceName The instance name.
     * @return mixed Object instance that was needed.
     * 
     */
    public function getInstance($instanceName)
    {
        // Return cache if possible
        if (isset($this->singletons[$instanceName]))
        {
            return $this->singletons[$instanceName];
        }
        
        $instanceName = $this->validateInstanceName($instanceName);
        $className = $this->extractClassName($instanceName);
        $configName = $this->extractConfigName($instanceName);
        
        if ($configName == 'NoProvider')
        {
            return $this->instantiateClassWithoutProvider('\\' . $className);
        }
        
        $providerClassName = $this->getProviderClassName($className, $instanceName);
        $providerMethodName = $this->providerMethodPrefix . $configName;
        $provider = $this->getProviderObject($providerClassName, $providerMethodName);
        $dependencies = $provider->getDependencies();
        
        // Get dependencies recursively
        foreach ($dependencies as $index => $dependencyInstanceName)
        {
            $dependencies[$index] = $this->getInstance($dependencyInstanceName);
        }
        
        $provider->setDependencies($dependencies);
        $object = $provider->$providerMethodName();
        
        if (!is_object($object))
        {
            $providerClassName = ltrim($providerClassName, '\\');
            throw new RuntimeException("DIC error in getting instance {$instanceName}, the provider method {$providerClassName}::{$providerMethodName}() does not return an object.");
        }
        
        if (get_class($object) !== $className)
        {
            $providerClassName = ltrim($providerClassName, '\\');
            throw new RuntimeException("DIC error in getting instance {$instanceName}, the provider method {$providerClassName}::{$providerMethodName}() does not return an instance of {$className}.");
        }
        
        // Save to cache if it has a singleton lifecycle
        if ($provider->isSingleton($configName))
        {
            $this->singletons[$instanceName] = $object;
        }
        
        return $object;
    }
    
    /**
     * Instantiate the class without constructor arguments.
     *
     * Throws RuntimeException if the class does not exist.
     *
     * @throws RuntimeException
     * @param string $className Fully qualified class name (with backslash prefix for safe instantiation).
     * @return object The instantiated class.
     *
     */
    protected function instantiateClassWithoutProvider($className)
    {
        if (!class_exists($className))
        {
            $className = ltrim($className, '\\');
            throw new RuntimeException("DIC error in getting instance '{$className}@NoProvider', class does not exist.");
        }
        
        return new $className;
    }
    
    /**
     * Throws exception if instance name is invalid.
     * 
     * Checks that the instance name has the '@' character as the
     * separator between class name and configuration name. Also
     * makes sure that both are not empty.
     * 
     * @throws RuntimeException
     * @param string $instanceName Instance name to be validated.
     * @return string Instance name (with any backslash prefix trimmed).
     *
     */
    protected function validateInstanceName($instanceName)
    {
        $instanceName = ltrim($instanceName, '\\');
        $instanceNameExploded = explode('@', $instanceName);
        
        if (count($instanceNameExploded) != 2 or
            empty($instanceNameExploded[0]) or
            empty($instanceNameExploded[1]))
        {
            throw new RuntimeException("DIC error in getting an instance, '{$instanceName}' is not a valid instance name.");
        }
        
        return $instanceName;
    }
    
    /**
     * Extracts class name from an instance name.
     *
     * Example extractions:
     *
     * <code>
     * Carrot\Core\FrontController@Main -> Carrot\Core\FrontController
     * Carrot\Database\MySQLi@Backup -> Carrot\Database\MySQLi
     * </code>
     *
     * @param string $instanceName
     *
     */
    protected function extractClassName($instanceName)
    {
        $instanceNameExploded = explode('@', $instanceName);
        return $instanceNameExploded[0];
    }
    
    /**
     * Extracts configuration name from an instance name.
     *
     * Example extractions:
     *
     * <code>
     * Carrot\Core\FrontController@Main -> Main
     * Carrot\Database\MySQLi@Backup -> Backup
     * </code>
     *
     * @param string $instanceName
     *
     */
    protected function extractConfigName($instanceName)
    {
        $instanceNameExploded = explode('@', $instanceName);
        return $instanceNameExploded[1];
    }
    
    /**
     * Returns the provider class name to be instantiated.
     * 
     * This method first searches provider to instance name bindings.
     * If none found it searches the provider to class bindings. If
     * none found, it then assumes that the provider class is in the
     * same namespace, having the same class name only with 'Provider'
     * suffix at the end. Example:
     *
     * <code>
     * Carrot\Core\FrontController -> Carrot\Core\FrontControllerProvider
     * Carrot\Database\MySQLi -> Carrot\Database\MySQLiProvider
     * </code>
     * 
     * @param string $className Fully qualified class name without backslash prefix.
     * @param string $instanceName The instance name of the class to be instantiated.
     * @return string Fully qualified class name of the provider (with backslash prefix for safe instantiation).
     *
     */
    protected function getProviderClassName($className, $instanceName)
    {
        if (array_key_exists($instanceName, $this->providerToInstanceNameBindings))
        {
            return '\\' . $this->providerToInstanceNameBindings[$instanceName];
        }
        
        if (array_key_exists($className, $this->providerToClassBindings))
        {
            return '\\' . $this->providerToClassBindings[$className];
        }
        
        return '\\' . $className . $this->providerClassSuffix;
    }
    
    /**
     * Instantiates and return the provider class.
     * 
     * Also checks whether the provider method needed are present,
     * throws a RuntimeException if it does not.
     *
     * @throws RuntimeException 
     * @param string $providerClassName The provider class name (with backslash prefix for safe instantiation).
     * @param string $providerMethodName The method name that must exist at the provider class.
     * @return \Carrot\Core\Interfaces\ProviderInterface An implementation of ProviderInterface.
     *
     */
    protected function getProviderObject($providerClassName, $providerMethodName)
    {   
        if (!class_exists($providerClassName))
        {
            $providerClassName = ltrim($providerClassName, '\\');
            throw new RuntimeException("DIC error in getting provider, the provider class {$providerClassName} doesn't exist.");
        }
        
        $provider = new $providerClassName;
        
        if (!($provider instanceof \Carrot\Core\Interfaces\ProviderInterface))
        {
            $providerClassName = ltrim($providerClassName, '\\');
            throw new RuntimeException("DIC error in getting provider. Provider class {$providerClassName} does not implement Carrot\Core\Interfaces\ProviderInterface.");
        }
        
        if (!method_exists($provider, $providerMethodName))
        {
            throw new RuntimeException("DIC error in getting provider configuration method. {$providerClassName}::{$providerMethodName}() doesn't exist.");
        }
        
        return $provider;
    }
}