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
 * 
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

use Carrot\Core\Interfaces\ProviderInterface;
use InvalidArgumentException;
use RuntimeException;
use Exception;
use ReflectionClass;
use ReflectionMethod;

class DependencyInjectionContainer
{
    /**
     * @var array Contains the constructor arguments bindings.
     */
    protected $bindings = array();
    
    /**
     * @var array Contains the instance name to provider bindings.
     */
    protected $providerBindings = array();
    
    /**
     * @var array Contains cache of objects with singleton lifecycles.
     */
    protected $cache = array();
    
    /**
     * Bind constructor arguments to an instance name.
     * 
     * An instance name is an identification you can use to identify
     * an object instantiation configuration. An instance name
     * contains the fully qualified class name, a configuration name
     * and a lifecycle setting. Example instance names:
     *
     * <code>
     * Carrot\Core\FrontController{Main:Transient}
     * Carrot\Database\MySQLi{Backup:Singleton}
     * </code>
     *
     * Use this method to bind an instance name to a set of 
     * constructor arguments. For example:
     *
     * <code>
     * $dic->bind('Carrot\Database\MySQLi{Main:Singleton}', array(
     *     'hostname',
     *     'username',
     *     'password',
     *     'database',
     * ));
     * </code>
     *
     * Subsequent calls to getInstance() will trigger the
     * instantiation of the class with the bound constructor
     * arguments and if it is configured to have singleton lifecycle,
     * the instance will be cached for future use.
     * 
     * @param string $instanceName The instance name to bind.
     * @param array $ctorArgs Array of constructor arguments.
     * 
     */
    public function bind($instanceName, array $ctorArgs)
    {
        $objectReference = new ObjectReference($instanceName);
        $className = $objectReference->getClassName();
        $configName = $objectReference->getConfigurationName();
        $lifecycle = $objectReference->getLifecycleSetting();
        $this->bindings[$className][$configName][$lifecycle]['object'] = $objectReference;
        $this->bindings[$className][$configName][$lifecycle]['args'] = $ctorArgs;
    }
    
    /**
     * Binds an instance name to a provider class.
     * 
     * The provider class must implement ProviderInterface. Any
     * dependencies of the provider class will be injected via the
     * constructor by reading @Inject annotation at the constructor's
     * doc block.
     * 
     * Provider bindings have higher priority than regular bindings
     * and will be used whenever it is available. 
     * 
     * @param string $instanceName The instance name to bind.
     * @param string $providerClassName Fully qualified class name to the provider class.
     *
     */
    public function bindProvider($instanceName, $providerInstanceName)
    {
        $objectReference = new ObjectReference($instanceName);
        $providerObjectReference = new ObjectReference($providerInstanceName);
        $className = $objectReference->getClassName();
        $configName = $objectReference->getConfigurationName();
        $lifecycle = $objectReference->getLifecycleSetting();
        $this->providerBindings[$className][$configName][$lifecycle]['object'] = $objectReference;
        $this->providerBindings[$className][$configName][$lifecycle]['provider'] = $providerObjectReference;
    }
    
    /**
     * Loads a configuration file.
     *
     * The loaded configuration file will have access to this DIC
     * instance through the $dic variable. Uses anonymous function to
     * load the file so that the file doesn't have access to this
     * class's protected methods.
     *
     * Throws InvalidArgumentException if the file doesn't exist.
     *
     * @throws InvalidArgumentException
     * @param string $filePath Absolute file path to the configuration file.
     *
     */
    public function loadConfigurationFile($filePath)
    {
        if (!file_exists($filePath))
        {
            throw new InvalidArgumentException("DIC error in loading configuration file, file '{$filePath}' does not exist.");
        }
        
        $loadFile = function($dic, $filePath)
        {
            require $filePath;
        };
        
        $loadFile($this, $filePath);
    }
    
    /**
     * Gets an instance of an instance name.
     * 
     * 
     * 
     * 
     * 
     * @param string $instanceName The instance name.
     * @return mixed Object instance that was needed.
     * 
     */
    public function getInstance(ObjectReference $objectReference)
    {
        $className = $objectReference->getClassName();
        $configName = $objectReference->getConfigurationName();
        $lifecycle = $objectReference->getLifecycleSetting();
        
        // Return cache if possible
        if (isset($this->cache[$className][$configName]))
        {
            return $this->cache[$className][$configName];
        }
        
        if (!class_exists($className))
        {
            throw new RuntimeException("DIC error in getting instance '{$instanceName}'. Class '{$className}' does not exist.");
        }
        
        if (isset($this->providerBindings[$className][$configName][$lifecycle]))
        {
            return $this->getInstanceFromProviderBindings($objectReference);
        }
        
        if (isset($this->bindings[$className][$configName][$lifecycle]))
        {
            return $this->getInstanceFromBindings($objectReference);
        }
        
        $instanceName = $objectReference->getInstanceName();
        return $this->tryToInstantiateWithoutParameters($className, $instanceName);
    }
    
    /**
     * 
     *
     */
    protected function tryToInstantiateWithoutParameters($className, $instanceName)
    {
        try
        {
            $object = new $className;
            return $object;
        }
        catch (Exception $e)
        {
            throw new RuntimeException("DIC error in getting instance. Instance '{$instanceName}' does not have bindings and the DIC fails to create the object without constructor parameters.");
        }
    }
    
    /**
     * Gets the instance from regular bindings, i.e. constructor arguments bindings.
     * 
     * 
     * 
     * @param ObjectReference $objectReference The object reference to the instance to get.
     * @return mixed Object instance that was needed.
     *
     */
    protected function getInstanceFromBindings(ObjectReference $objectReference)
    {
        $className = $objectReference->getClassName();
        $configName = $objectReference->getConfigurationName();
        $lifecycle = $objectReference->getLifecycleSetting();
        $ctorArgs = array();
        
        foreach ($this->bindings[$className][$configName][$lifecycle]['args'] as $argument)
        {
            if ($argument instanceof ObjectReference)
            {
                $ctorArgs[] = $this->getInstance($argument);
            }
            
            $ctorArgs[] = $argument;
        }
        
        return $this->createObject($className, $ctorArgs);
    }
    
    /**
     * Gets the instance from the provider classes.
     * 
     * 
     * 
     * @param ObjectReference $objectReference The object reference to the instance to get.
     * @return mixed Object instance that was needed.
     *
     */
    protected function getInstanceFromProviderBindings(ObjectReference $objectReference)
    {
        $className = $objectReference->getClassName();
        $configName = $objectReference->getConfigurationName();
        $lifecycle = $objectReference->getLifecycleSetting();
        $providerObjectReference = $this->bindings[$className][$configName][$lifecycle]['provider'];
        $provider = $this->getInstance($providerObjectReference);
        
        if (!($provider instanceof ProviderInterface))
        {
            $providerClassName = $providerObjectReference->getClassName();
            throw new RuntimeException("DIC error in getting instance. Provider class '{$providerClassName}' does not implement the required interface (Carrot\Core\Interfaces\ProviderInterface).");
        }
        
        $object = $provider->get();
        
        if (!($object instanceof $className))
        {
            $providerClassName = $providerObjectReference->getClassName();
            throw new RuntimeException("DIC error in getting instance. Provider class '{$providerClassName}' does not return an instance of '{$className}'.");
        }
        
        return $object;
    }
    
    /**
     * Creates the object with dynamic number of constructor arguments.
     * 
     * We all know that reflection is slow, so this method tries to
     * avoid it using the ugly switch statement. According to the
     * the interwebs it's three times as fast as using reflection.
     * 
     * @see http://blog.liip.ch/archive/2008/09/18/fotd-reflectionclass-newinstanceargs-args-is-slow.html
     * @param string $className Fully qualified class name without backslash prefix.
     * @param array $ctorArgs The constructor arguments array.
     *
     */
    protected function createObject($className, array $args)
    {
        $count = count($args);
        
        switch ($count)
        {
            case 0:
                return new $className;
            break;
            case 1:
                return new $className($args[0]);
            break;
            case 2:
                return new $className($args[0], $args[1]);
            break;
            case 3:
                return new $className($args[0], $args[1], $args[2]);
            break;
        }
        
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($args);
    }
}