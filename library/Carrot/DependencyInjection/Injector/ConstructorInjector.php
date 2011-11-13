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
 * Constructor injector.
 *
 * Injects dependencies given via the constructor. This object
 * will generate a DependencyList from the given arguments, which
 * allows the container to do a recursive instantiation.
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\DependencyInjection\Injector;

use ReflectionClass,
    Carrot\DependencyInjection\Reference,
    Carrot\DependencyInjection\DependencyList;

class ConstructorInjector implements InjectorInterface
{
    /**
     * @var Reference Refers to the object this injector is supposed
     *      to instantiate.
     */
    protected $reference;
    
    /**
     * @var array The list of arguments to be used for instantiation.
     */
    protected $args;
    
    /**
     * @var DependencyList The dependency list of this injector,
     *      generated from {@see $args}.
     */
    protected $dependencyList;
    
    /**
     * Constructor.
     * 
     * Pass the list of constructor arguments you wish to be injected
     * at instantiation:
     *
     * <code>
     * $requestInjector = new ConstructorInjector(array(
     *     $_SERVER,
     *     $_GET,
     *     $_POST,
     *     ...
     * ));
     * </code>
     *
     * If the dependency is an instance of another object, pass the
     * Reference to that dependency:
     *
     * <code>
     * $controllerInjector = new ConstructorInjector(array(
     *     new Reference('Acme\App\Model'),
     *     new Reference('Acme\App\View')
     * ));
     * </code>
     *
     * The injector will generate an instance of DependencyList from
     * the provided arguments, so that the container will instantiate
     * the dependencies recursively.
     * 
     * @param Reference $reference Refers to the object this injector
     *        is supposed to instantiate.
     * @param array $args The constructor arguments to be used for
     *        instantiation.
     *
     */
    public function __construct(Reference $reference, array $args)
    {
        $list = array();
        
        foreach ($args as $value)
        {
            if ($value instanceof Reference)
            {
                $list[] = $value;
            }
        }
        
        $this->reference = $reference;
        $this->args = $args;
        $this->dependencyList = new DependencyList($list);
    }
    
    /**
     * Get the list of dependencies needed to perform instantiation.
     * 
     * Returns the DependencyList instance generated from the
     * arguments provided at object construction.
     * 
     * @return DependencyList
     *
     */
    public function getDependencyList()
    {
        return $this->dependencyList;
    }
    
    /**
     * Wire the dependencies provided to the object, returning the
     * instantiated object.
     * 
     * @see generateConstructorArguments()
     * @see instantiate()
     * @param DependencyList $dependencyList The dependency list of
     *        this class, with all dependencies fulfilled.
     * @return mixed The object that this injector is supposed to
     *         instantiate.
     *
     */
    public function inject(DependencyList $dependencyList)
    {   
        $ctorArgs = $this->generateConstructorArguments($dependencyList);
        return $this->instantiate($ctorArgs);
    }
    
    /**
     * Get the reference for the instance this injector is supposed
     * to instantiate.
     * 
     * @return Reference
     *
     */
    public function getReference()
    {
        return $this->reference;
    }
    
    /**
     * Combines {@see $args} class property and the DependencyList
     * instance provided into a constructor arguments array, ready
     * for usage.
     * 
     * Loops through {@see $args} class property and changes each
     * instance of Reference into an instantiated dependency instance
     * retrieved from the provided DependencyList.
     * 
     * @see inject()
     * @param DependencyList $dependencyList 
     * @return array Arguments array ready to be used for
     *         instantiation.
     * 
     */
    protected function generateConstructorArguments(DependencyList $dependencyList)
    {
        $ctorArgs = array();
        
        foreach ($this->args as $value)
        {
            if ($value instanceof Reference)
            {
                $ctorArgs[] = $dependencyList->getInstantiatedDependency($value);
                continue;
            }
            
            $ctorArgs[] = $value;
        }
        
        return $ctorArgs;
    }
    
    /**
     * Instantiates the object with the given constructor arguments.
     * 
     * We all know that reflection is slow, so this method tries to
     * avoid it using the ugly switch statement. According to the
     * the interwebs it's three times as fast as using reflection.
     * 
     * @see inject()
     * @see http://blog.liip.ch/archive/2008/09/18/fotd-reflectionclass-newinstanceargs-args-is-slow.html
     * @param array $ctorArgs Constructor arguments, ready for usage.
     *
     */
    protected function instantiate($ctorArgs)
    {
        $className = $this->reference->getClass();
        $count = count($ctorArgs);
        
        switch ($count)
        {
            case 0:
                return new $className;
            break;
            case 1:
                return new $className($ctorArgs[0]);
            break;
            case 2:
                return new $className($ctorArgs[0], $ctorArgs[1]);
            break;
            case 3:
                return new $className($ctorArgs[0], $ctorArgs[1], $ctorArgs[2]);
            break;
            case 4:
                return new $className($ctorArgs[0], $ctorArgs[1], $ctorArgs[2], $ctorArgs[3]);
            break;
            case 5:
                return new $className($ctorArgs[0], $ctorArgs[1], $ctorArgs[2], $ctorArgs[3], $ctorArgs[4]);
            break;
            case 6:
                return new $className($ctorArgs[0], $ctorArgs[1], $ctorArgs[2], $ctorArgs[3], $ctorArgs[4], $ctorArgs[5]);
            break;
            case 7:
                return new $className($ctorArgs[0], $ctorArgs[1], $ctorArgs[2], $ctorArgs[3], $ctorArgs[4], $ctorArgs[5], $ctorArgs[6]);
            break;
            case 8:
                return new $className($ctorArgs[0], $ctorArgs[1], $ctorArgs[2], $ctorArgs[3], $ctorArgs[4], $ctorArgs[5], $ctorArgs[6], $ctorArgs[7]);
            break;
            case 9:
                return new $className($ctorArgs[0], $ctorArgs[1], $ctorArgs[2], $ctorArgs[3], $ctorArgs[4], $ctorArgs[5], $ctorArgs[6], $ctorArgs[7], $ctorArgs[8]);
            break;
            case 10:
                return new $className($ctorArgs[0], $ctorArgs[1], $ctorArgs[2], $ctorArgs[3], $ctorArgs[4], $ctorArgs[5], $ctorArgs[6], $ctorArgs[7], $ctorArgs[8], $ctorArgs[9]);
            break;
        }
        
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($ctorArgs);
    }
}