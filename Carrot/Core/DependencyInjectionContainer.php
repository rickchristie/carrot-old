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
 * Carrot uses this class to wire the dependencies of almost all
 * of the core class and your classes. You can bind constructor
 * arguments to an instance name:
 *
 * <code>
 * $dic->bind('Carrot\Database\MySQL{Logging:Singleton}', array(
 *     'hostname',
 *     'username',
 *     'password',
 *     'database'
 * ));
 * </code>
 *
 * The instance name consists of fully qualified class name, the
 * configuration name and the configuration lifecycle. The
 * lifecycle can be 'Transient' or 'Singleton'. If set to
 * transient, new instance is created every time it is needed. If
 * set to singleton one instance is created, cached, and returned.
 *
 * <code>
 * Carrot\Database\MySQL{Logging:Singleton}
 * Carrot\Core\Request{Main:Transient}
 * </code>
 *
 * Object references in the constructor arguments will be
 * converted to actual instances. For example, if your controller
 * has a dependency to your model:
 *
 * <code>
 * $dic->bind('App\Controller{Main:Transient}', array(
 *     new ObjectReference('App\Model{Main:Singleton}')
 * ));
 * </code>
 *
 * The object graph is built recursively so beware of infinite
 * loops. After everything is bound, you can get the instance:
 *
 * <code>
 * $dic->getInstance(new ObjectReference('App\Controller{Main:Transient}'));
 * </code>
 *
 * If you need logic on wiring the dependencies, create a provider
 * class (implement ProviderInterface):
 *
 * <code>
 * namespace App;
 *
 * use Carrot\Core\Interfaces\ProviderInterface;
 * 
 * class ControllerProvider implements ProviderInterface
 * {
 *     public function get()
 *     {
 *         // Logic goes here..
 *         return new Controller($model);
 *     }
 * }
 * </code>
 *
 * and bind the provider:
 *
 * <code>
 * $dic->bindProvider('App\Controller{Main:Transient}', 'App\ControllerProvider{Main:Transient}');
 * </code>
 *
 * If your provider class has dependencies, don't forget to bind
 * it too. Provider bindings gets a priority over regular
 * constructor argument bindings.
 *
 * If no bindings were found, the DIC will try to create the class
 * without constructor parameters.
 *
 * For more information, please see the docs for
 * {@see Carrot\Core\Interfaces\ProviderInterface}.
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
     * Carrot\Core\Request{Main:Transient}
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
     * Binds an instance name to a provider instance name.
     * 
     * The provider class must implement ProviderInterface. This is
     * useful if you need logic in wiring dependencies. If your
     * provider has dependencies, also bind it using bind().
     *
     * <code>
     * $dic->bindProvider('App\Controller{Main:Transient}', 'App\ControllerProvider{Main:Transient}');
     * </code>
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
     * Gets an instance of an instance name.
     * 
     * If a provider binding exists, it will be used. Otherwise it
     * will use regular constructor argument bindings. If it also
     * doesn't exist, this method will try to create the object
     * without any instantiation parameters.
     * 
     * <code>
     * $model = $dic->getInstance(new ObjectReference('App\Model{Main:Singleton}'));
     * </code>
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
        
        // Returns cache if possible
        if (isset($this->cache[$className][$configName][$lifecycle]))
        {
            return $this->cache[$className][$configName][$lifecycle];
        }
        
        if (!class_exists($className))
        {
            $instanceName = $objectReference->getInstanceName();
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
        
        return $this->tryToInstantiateWithoutParameters($objectReference);
    }
    
    /**
     * Tries to instantiate the object without any constructor parameter.
     *
     * Throws RuntimeException if it fails to instantiate the object
     * without constructor parameters.
     * 
     * @throws RuntimeException
     * @param ObjectReference $objectReference The object reference to the instance to get.
     * @return mixed Object instance that was needed.
     * 
     */
    protected function tryToInstantiateWithoutParameters(ObjectReference $objectReference)
    {
        $className = $objectReference->getClassName();
        $configName = $objectReference->getConfigurationName();
        $lifecycle = $objectReference->getLifecycleSetting();
        
        try
        {
            $object = new $className;
        }
        catch (Exception $e)
        {
            $instanceName = $objectReference->getInstanceName();
            throw new RuntimeException("DIC error in getting instance. Instance '{$instanceName}' does not have bindings and the DIC fails to create the object without constructor parameters.");
        }
        
        if ($lifecycle == 'Singleton')
        {
            $this->cache[$className][$configName][$lifecycle] = $object;
        }
        
        return $object;
    }
    
    /**
     * Gets the instance from regular bindings, i.e. constructor arguments bindings.
     * 
     * This method loops through the arguments and converts instances
     * of object references to actual instances of the referred
     * object. If the lifecycle is singleton, a cache of an
     * instantiated object is saved.
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
                continue;
            }
            
            $ctorArgs[] = $argument;
        }
        
        $object = $this->createObject($className, $ctorArgs);
        
        if ($lifecycle == 'Singleton')
        {
            $this->cache[$className][$configName][$lifecycle] = $object;
        }
        
        return $object;
    }
    
    /**
     * Gets the instance from the provider classes.
     * 
     * The provider object is obtained using getInstance(), so any
     * dependencies should get sorted out. After that
     * ProviderInterface::get() is called and if it's singleton it's
     * saved to cache.
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
        
        if ($lifecycle == 'Singleton')
        {
            $this->cache[$className][$configName][$lifecycle] = $object;
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
            case 4:
                return new $className($args[0], $args[1], $args[2], $args[3]);
            break;
            case 5:
                return new $className($args[0], $args[1], $args[2], $args[3], $args[4]);
            break;
            case 6:
                return new $className($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
            break;
            case 7:
                return new $className($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
            break;
            case 8:
                return new $className($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
            break;
            case 9:
                return new $className($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]);
            break;
            case 10:
                return new $className($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]);
            break;
        }
        
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($args);
    }
}