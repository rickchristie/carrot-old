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
 * Dependency injection container.
 *
 * Responsible for utilizing injection configuration
 * implementations and injectors to build the dependency graph
 * of the needed object instance. Also responsible for storing
 * cache of singleton objects, and since user classes are all
 * instantiated by this object, this ensures the singleton
 * objects are instantiated once only.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection;

use RuntimeException,
    Carrot\Core\DependencyInjection\Config\ConfigInterface,
    Carrot\Core\DependencyInjection\Injector\InjectorInterface;

class Container
{
    /**
     * @var ConfigInterface The dependency injection configuration.
     */
    protected $config;
    
    /**
     * @var array Contains cache of objects with singleton lifecycle.
     */
    protected $singletons;
    
    /**
     * @var array The stack of objects that needs to be instantiated {@see initializeStack()}.
     */
    protected $stack = array();
    
    /**
     * Constructor.
     * 
     * @param ConfigInterface $config The dependency injection configuration.
     *
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    
    /**
     * Get an instance of the given reference.
     *
     * Will utilize the configuration object and loop through each of
     * the object's dependencies and instantiate them until the
     * dependency graph of the given object is fully built.
     *
     * Ideally, this method should use a recursive call, which would
     * result in a more readable and maintainable code. However, PHP
     * recommends us to avoid recursive function/method call with
     * more than 100-200 recursion levels as it can smash the stack
     * {@see http://www.php.net/manual/en/functions.user-defined.php}.
     * Since placing a cap for dependency level is not an option, we
     * are forced to use normal loop and simulate a stack to build
     * the entire dependency graph recursively.
     * 
     * @param Reference $reference The reference to the object whose instance we wanted to get.
     * @return mixed The object that was inquired.
     *
     */
    public function get(Reference $reference)
    {
        $this->stack = array();
        $this->addToStack($reference, NULL);
        
        while (!empty($this->stack))
        {   
            $topmostItem = end($this->stack);
            $topmostStackIndex = key($this->stack);
            
            if ($this->canUseSingletonCache($topmostItem['reference']))
            {   
                $instance = $this->getFromSingletonCache($topmostItem['reference']);
                
                if ($this->isDependencyOfAnotherObject($topmostItem))
                {
                    $this->addInstantiatedObjectToDependencyList($topmostItem, $instance);
                    $this->removeStack($topmostStackIndex);
                    continue;
                }
                
                return $instance;
            }
            
            if ($topmostItem['dependencyList']->areAllDependenciesFulfilled())
            {
                $instance = $this->instantiateUsingInjector($topmostItem);
                
                if ($this->isDependencyOfAnotherObject($topmostItem))
                {
                    $this->addInstantiatedObjectToDependencyList($topmostItem, $instance);
                    $this->removeStack($topmostStackIndex);
                    continue;
                }
                
                return $instance;
            }
            
            $this->addDependenciesToStack($topmostItem['dependencyList'], $topmostStackIndex);
        }
    }
    
    /**
     * Add a reference to the stack.
     *
     * The stack is an array we use to store the list of objects we
     * need to instantiate. The first entry to the stack is the
     * object we wanted to get. The second and the rest of the stack
     * are the object's dependencies. Each loop in {@see get()} will
     * try to instantiate the topmost stack until it finds an item
     * from the stack that is not a dependency. It will then
     * instantiate the said object and return it.
     *
     * Example stack array structure:
     *
     * <code>
     * $stack = array(
     *     'Acme\App\Controller{Singleton:}' => array(
     *         'reference' => $reference,
     *         'injector' => $injector,
     *         'dependencyOf' => NULL,
     *         'dependencyList' => $dependencyList
     *     ),
     *     'Carrot\Core\Request{Singleton:}' => array(
     *         'reference' => $reference,
     *         'injector' => $injector,
     *         'dependencyOf' => 'Acme\App\Controller{Singleton:}',
     *         'dependencyList' => $dependencyList
     *     ),
     *     ...
     * );
     * </code>
     * 
     * @see get()
     * @throws RuntimeException When there is a circular dependency.
     * @param Reference $reference Reference to be added to the
     *        stack.
     * @param string|NULL $dependencyOf The stack index of the item
     *        where the given reference is a dependency of, NULL if
     *        the reference given is not a dependency of another
     *        stack item.
     *
     */
    protected function addToStack(Reference $reference, $dependencyOf)
    {
        $id = $reference->getID();
        
        if (array_key_exists($id, $this->stack))
        {
            $circularDependencyCulpritID = $this->stack[$dependencyOf]['reference']->getID();
            throw new RuntimeException("Container error in getting an instance. Encountered circular dependency in '{$id}' and '{$circularDependencyCulpritID}'.");
        }
        
        $injector = $this->config->getInjector($reference);
        $dependencyList = $injector->getDependencyList();
        $this->stack[$id] = array(
            'reference' => $reference,
            'injector' => $injector,
            'dependencyOf' => $dependencyOf,
            'dependencyList' => $dependencyList
        );
    }
    
    /**
     * Adds an instantiated dependency to the appropriate
     * DependencyList instance of the given stack item.
     *
     * @see get()
     * @param array $item The stack item to add the dependency
     *        instance to.
     * @param mixed $instance The instance to be added to the
     *        dependency list.
     *
     */
    protected function addInstantiatedObjectToDependencyList(array $item, $instance)
    {
        $parentStackIndex = $item['dependencyOf'];
        $dependencyList = $this->stack[$parentStackIndex]['dependencyList'];
        $dependencyList->setInstantiatedDependency(
            $item['reference'],
            $instance
        );
    }
    
    /**
     * Removes an item from the stack.
     * 
     * @see get()
     * @param string $stackIndex The index of the stack item to be
     *        removed.
     *
     */
    protected function removeStack($stackIndex)
    {
        unset($this->stack[$stackIndex]);
    }
    
    /**
     * Checks if the container can use an instance from the singleton
     * objects cache for the given Reference instance.
     *
     * This method just checks if a cache exists in
     * {@see $singletons} class property. It doesn't check if the
     * current item has singleton lifecycle since only singleton
     * instances are saved in the cache.
     *
     * @see get()
     * @param Reference $reference
     * @return bool TRUE if it can, FALSE otherwise.
     *
     */
    protected function canUseSingletonCache(Reference $reference)
    {
        return isset($this->singletons[$reference->getID()]);
    }
    
    /**
     * Get an instance from the singleton objects cache.
     *
     * @see get()
     * @param Reference $reference The reference to the object to be
     *        retrieved.
     * @return mixed The object instance.
     *
     */
    protected function getFromSingletonCache(Reference $reference)
    {
        return $this->singletons[$reference->getID()];
    }
    
    /**
     * Checks if an item in the stack is a dependency to another
     * object in the stack.
     *
     * If the stack item is not a dependency of another object in the
     * stack, then it can be assumed that the given stack item is the
     * first stack item, since all other stack items are there only
     * because they are a direct/indirect dependency of the
     * first stack item.
     * 
     * @see get()
     * @param array $item The stack item to be checked.
     * @return bool TRUE if the stack item is a dependency, FALSE
     *         otherwise.
     *
     */
    protected function isDependencyOfAnotherObject(array $item)
    {
        return (is_null($item['dependencyOf']) == FALSE);
    }
    
    /**
     * Add dependencies from the DependencyList instance given to the
     * stack.
     *
     * @see get()
     * @see addToStack()
     * @param DependencyList $dependencyList The dependency list to
     *        be added to the stack.
     * @param index $dependencyOf The index of the stack item that owns
     *        the given dependency list.
     *
     */
    protected function addDependenciesToStack(DependencyList $dependencyList, $dependencyOf)
    {
        foreach ($dependencyList->getList() as $reference)
        {
            $this->addToStack($reference, $dependencyOf);
        }
    }
    
    /**
     * Runs the {@see InjectorInterface::inject()} method and returns
     * the instantiated object.
     *
     * @throws RuntimeException If the object returned from the
     *         injector is not instance of the class that the stack
     *         item's refers to.
     * @param array $item The stack item to be instantiated.
     *
     */
    protected function instantiateUsingInjector(array $item)
    {
        $instance = $item['injector']->inject($item['dependencyList']);
        $class = $item['reference']->getClass();
        $id = $item['reference']->getID();
        
        if ($instance instanceof $class == FALSE)
        {
            $unexpectedType = (is_object($instance)) ? get_class($instance) : gettype($instance);
            throw new RuntimeException("Container error in trying to instantiate '{$id}'. The injector doesn't return the appropriate object. Instance of {$class} expected, {$unexpectedType} returned.");
        }
        
        if ($item['reference']->isSingleton())
        {
            $this->singletons[$id] = $instance;
        }
        
        return $instance;
    }
}