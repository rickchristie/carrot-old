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
 * Bindings injection configuration.
 *
 * The injection configuration in this object is set by binding
 * each class/interface to a Reference instance. When the
 * container queries for injector instances, this object will
 * read the constructor arguments of the object to be
 * instantiated using reflection and construct a
 * ConstructorInjector instance on the fly to be returned. This
 * allows the user to use the dependency injection container
 * with minimal configuration writing.
 *
 * Since this class uses PHP reflection heavily, expect a
 * performance to development convenience trade-off. It is
 * suggested to use this configuration object in development and
 * switch to another in production.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\DependencyInjection\Config;

use ReflectionMethod,
    ReflectionClass,
    Exception,
    RuntimeException,
    Carrot\DependencyInjection\Reference,
    Carrot\DependencyInjection\Injector\InjectorInterface,
    Carrot\DependencyInjection\Injector\ConstructorInjector;

class BindingsConfig implements ConfigInterface
{
    /**
     * @var array List of explicitly set injectors.
     */
    protected $injectors = array();
    
    /**
     * @var array List of Reference instances bound, {@see bind()}.
     */
    protected $bindings = array();
    
    /**
     * Add an injector.
     * 
     * This method explicitly set the injector instance used.
     * Injectors set this way has the highest priority. Injector
     * classes are saved per their reference instance ID. You can
     * remove explicitly set injector using {@see removeInjector()}.
     * 
     * @param InjectorInterface $injector The injector to be added.
     *
     */
    public function addInjector(InjectorInterface $injector)
    {
        $id = $injector->getReference()->getID();
        $this->injectors[$id] = $injector;
    }
    
    /**
     * Remove injectors explicitly set to the given Reference
     * instance.
     * 
     * @param Reference $reference The reference of the injector to
     *        be removed.
     *
     */
    public function removeInjector(Reference $reference)
    {
        $id = $reference->getID();
        unset($this->injectors[$id]);
    }
    
    /**
     * Bind a default Reference instance to be used when the given
     * class name is encountered, for the given namespace.
     *
     * Configuration is done by binding a Reference class as a
     * default. For example, you can tell the configuration object to
     * always use the 'Main' PHP MySQLi instance everytime it is
     * encountered:
     *
     * <code>
     * // Use constructor injector for MySQLi dependencies.
     * $config->setInjector(
     *     new ConstructorInjector(
     *         new Reference(
     *             'MySQLi',
     *             'Singleton',
     *             'Main'
     *         ),
     *         array(
     *             'localhost',
     *             'username',
     *             'password',
     *             'database'
     *         )
     *     )
     * );
     * 
     * // Set the previously defined 'Main' MySQLi instance as the
     * // default instance to be used whenever a dependency to
     * // MySQLi is encountered.
     * $config->bind(
     *     'MySQLi',
     *     new Reference(
     *         'MySQLi',
     *         'Singleton',
     *         'Main'
     *     ) 
     * );
     * </code>
     * 
     * With the above setting, for every constructor argument typed
     * as MySQLi, this configuration object will create a
     * ConstructorInjector instance that, when properly handled by
     * the container, will inject the MySQLi instance to it.
     *
     * If you use unnamed reference for the MySQLi instance, you
     * don't need to bind, for every constructor argument that
     * doesn't have bindings, this configuration object will assume
     * that it's a dependency to the default unnamed instance. In
     * this instance, you only need to do this:
     * 
     * <code>
     * // Set constructor injector for MySQLi unnamed reference.
     * $config->setInjector(
     *     new ConstructorInjector(
     *         new Reference('MySQLi'),
     *         array(
     *             'localhost',
     *             'username',
     *             'password',
     *             'database'
     *         )
     *     )
     * );
     *
     * // We don't need to do bindings after this because every
     * // constructor argument typed as MySQLi will be converted
     * // into unnamed reference to MySQLi as default.
     * </code>
     * 
     * You can also bind for a specific namespace. This is useful,
     * for example, if you want every class under Acme\Logging
     * namespace to use 'Logging' MySQLi instance, while other
     * classes use 'Main' MySQLi instance which connects to another
     * server. You can bind 'Main' MySQLi instance as default
     * instance to be injected, and bind 'Logging' instance
     * specifically for Acme\Logging namespace:
     * 
     * <code>
     * $config->bind(
     *     'MySQLi',
     *     new Reference(
     *         'MySQLi',
     *         'Singleton',
     *         'Main'
     *     )
     * );
     *
     * $config->bind(
     *     'MySQLi',
     *     new Reference(
     *         'MySQLi',
     *         'Singleton',
     *         'Logging'
     *     ),
     *     'Acme\Logging'
     * );
     * </code>
     * 
     * If there are two namespace bindings that apply, the most
     * specific namespace wins.
     * 
     * @param Reference $reference The default Reference instance.
     * @param string $className Fully qualified class name where the
     *        given Reference instance is to be set as default.
     *        Defaults to the class of the Reference instance given.
     * @param string $namespace The namespace for the binding to take
     *        effect. Defaults to root '\', which applies for every
     *        namespace.
     *
     */
    public function bind(Reference $reference, $className = NULL, $namespace = '\\')
    {
        if ($className == NULL)
        {
            $className = $reference->getClass();
        }
        
        $className = trim($className, '\\');
        $namespace = trim($namespace, '\\');
        $namespace .= '\\';
        $id = $reference->getID();
        $this->bindings[$className][$namespace] = $reference;
    }
    
    /**
     * Get the injector that instantiates the object referred to by
     * the given Reference instance.
     *
     * This method will first check for explicit injectors. If there
     * are none set, it will generate a ConstructorInjector instance
     * on the fly using bindings configuration. This method will
     * create an injector that always tries to satisfy the object's
     * dependencies, even if the said dependencies is allowed to be
     * NULL. If you need the injector to behave differently, you can
     * explicitly set an injector instance using
     * {@see setInjector()}.
     * 
     * @param Reference $reference Refers to the instance whose
     *        injector is to be returned.
     * @return InjectorInterface The injector for the given Reference
     *         instance.
     *
     */
    public function getInjector(Reference $reference)
    {
        $id = $reference->getID();
        
        if ($this->hasExplicitInjector($id))
        {
            return $this->injectors[$id];
        }
        
        $className = $reference->getClass();
        $ctorArgs = $this->getConstructorArguments($className, $id);
        return new ConstructorInjector(
            $reference,
            $ctorArgs
        );
    }
    
    /**
     * Check if the given instance ID has an injector instance
     * explicitly set for it.
     *
     * @param string $id The instance ID to be checked.
     * @return bool TRUE is it has, FALSE otherwise.
     *
     */
    protected function hasExplicitInjector($id)
    {
        return isset($this->injectors[$id]);
    }
    
    /**
     * Get constructor arguments to be used to instantiate
     * ConstructorInjector for the given class name.
     *
     * This method uses reflection to find out constructor arguments
     * of the given class, converts them into instances of Reference
     * using {@see getReferenceFromBindings()}, resulting in an array
     * that is ready to be used to instantiate the proper
     * ConstructorInjector instance for the given class name.
     * 
     * The returned array contains information on which class needs
     * to be injected to the constructor and the order of the
     * injection. Example returned array structure:
     * 
     * <code>
     * $ctorArgs = array(
     *     $referenceA,
     *     $referenceB
     * );
     * </code>
     * 
     * @throws RuntimeException If the class contains primitive
     *         variable type arguments (which this object would not
     *         be able to solve).
     * @param string $className Fully qualified class name of the
     *        class whose constructor arguments is to be retrieved.
     * @param string $id The instance ID whose constructor arguments
     *        is to be retrieved.
     * @return array
     *
     */
    protected function getConstructorArguments($className, $id)
    {   
        if (method_exists($className, '__construct') == FALSE)
        {
            return array();
        }
        
        $reflectionClass = new ReflectionClass($className);
        
        if ($reflectionClass->isInterface())
        {
            throw new RuntimeException("BindingsConfig failed to generate constructor arguments for {$id}. It's an interface and isn't bound to any concrete implementation.");
        }
        
        $method = new ReflectionMethod($className, '__construct');
        $parameters = $method->getParameters();
        $ctorArgs = array();
        
        foreach ($parameters as $param)
        {
            try
            {
                $dependencyClassReflection = $param->getClass();
            }
            catch (Exception $exception)
            {
                $message = $exception->getMessage();
                throw new RuntimeException("BindingsConfig failed to generate a constructor injector for {$id} because: {$message}.");
            }
            
            if ($dependencyClassReflection instanceof ReflectionClass == FALSE)
            {
                $paramName = $param->getName();
                throw new RuntimeException("BindingsConfig failed to generate a constructor injector for {$id}. The class constructor requires a non-object argument for '{$paramName}' and the configuration object is unable provide it.");
            }
            
            $ctorArgs[] = $this->getReferenceFromBindings(
                $dependencyClassReflection->getName(),
                $className
            );
        }
        
        return $ctorArgs;
    }
    
    /**
     * Determines which Reference instance to be returned from the
     * bindings configuration.
     *
     * This method will try to find out which Reference instance to
     * be returned by reading the bindings configuration. If there
     * are none, it returns an unnamed reference as default.
     * 
     * @param string $dependencyClassName Fully qualified class name
     *        of the class whose reference is to be retrieved.
     * @param string $context Fully qualified class name of the class
     *        which needs the dependency.
     * @return Reference
     *
     */
    protected function getReferenceFromBindings($dependencyClassName, $className)
    {   
        if (!isset($this->bindings[$dependencyClassName]))
        {
            return new Reference($dependencyClassName);
        }
        
        $boundReference = NULL;
        $boundNamespace = NULL;
        
        foreach ($this->bindings[$dependencyClassName] as $namespace => $reference)
        {
            $namespaceLength = strlen($namespace);
            $boundNamespaceLength = strlen($boundNamespace);
            $isCurrentNamespaceMoreSpecific = $namespaceLength > $boundNamespaceLength;
            
            if ($isCurrentNamespaceMoreSpecific == FALSE)
            {
                continue;
            }
            
            if ($namespace == '\\')
            {
                $boundNamespace = $namespace;
                $boundReference = $reference;
                continue;
            }
            
            if (substr($className, 0, $namespaceLength) == $namespace)
            {   
                $boundNamespace = $namespace;
                $boundReference = $reference;
            }
        }
        
        if (empty($boundReference))
        {
            return new Reference($dependencyClassName);
        }
        
        return $boundReference;
    }
}