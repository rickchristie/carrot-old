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
 * Callback injector.
 *
 * Wires dependencies using an anonymous function. The anonymous
 * function can accept arguments, which are processed in a way
 * similar to arguments array are processed in
 * ConstructorInjector.
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\DependencyInjection\Injector;

use RuntimeException,
    Carrot\DependencyInjection\Reference,
    Carrot\DependencyInjection\DependencyList;

class CallbackInjector implements InjectorInterface
{
    /**
     * @var Reference Refers to the object this injector is supposed
     *      to instantiate.
     */
    protected $reference;
    
    /**
     * @var callback The anonymous function that wires dependencies
     *      and instantiates the object.
     */
    protected $callback;
    
    /**
     * @var array Arguments to be passed when calling the callback.
     */
    protected $args;
    
    /**
     * @var DependencyList The dependency list of this injector, it
     *      is generated from the callback arguments.
     */
    protected $dependencyList;
    
    /**
     * Constructor.
     * 
     * Pass the anonymous function and the arguments needed to run
     * the function in construction:
     *
     * <code>
     * $injector = new CallbackInjector(
     *     new Reference('Acme\App\Controller'),
     *     function($id)
     *     {
     *         return new Acme\App\Controller($id);
     *     },
     *     array('VAL$3271')
     * );
     * </code>
     *
     * The arguments will be passed to the anonymous function based
     * on the order of the array given. Reference instances will be
     * marked as dependencies and thus will be instantiated by the
     * container and passed to the callback by this class:
     *
     * <code>
     * $injector = new CallbackInjector(
     *     new Reference('Acme\App\Controller'),
     *     function(Carrot\Request $request)
     *     {
     *         return new Acme\App\Controller(
     *             $request->getServer['REQUEST_URI']
     *         )
     *     },
     *     array(
     *         new Reference('Carrot\Request')
     *     )
     * );
     * </code>
     * 
     * @throws InvalidArgumentException If the callback provided is
     *         not callable or if it's an array.
     * @param Reference $reference Refers to the object this injector
     *        is supposed to instantiate.
     * @param callback $callback The anonymous function that wires
     *        dependencies.
     * @param array $args Arguments to be passed when running the
     *        callback.
     *
     */
    public function __construct(Reference $reference, $callback, array $args = array())
    {
        if (!is_callable($callback))
        {
            throw new InvalidArgumentException("CallbackInjector error in instantiation. The provided callback is not a callable.");
        }
        
        if (is_array($callback))
        {
            throw new InvalidArgumentException("CallbackInjector error in instantiation. The provided callback must be an anonymous function.");
        }
        
        $list = array();
        
        foreach ($args as $value) 
        {
            if ($value instanceof Reference)
            {
                $list[] = $value;
            }
        }
        
        $this->reference = $reference;
        $this->callback = $callback;
        $this->args = $args;
        $this->dependencyList = new DependencyList($list);
    }
    
    /**
     * Get the list of dependencies needed to perform instantiation.
     *
     * Returns the DependencyList instance generated from the
     * arguments provided at object construction.
     * 
     * @see DependencyList
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
     * Generates callback arguments and runs the callback. The return
     * value of the argument is then returned to the container.
     * 
     * @param DependencyList $dependencyList The dependency list of
     *        this class, with all dependencies fulfilled.
     * @return mixed The object that this injector is supposed to
     *         instantiate.
     *
     */
    public function inject(DependencyList $dependencyList)
    {
        $class = $this->reference->getClass();
        $callbackArgs = $this->generateCallbackArguments($dependencyList);
        $object = $this->runCallback($callbackArgs);
        return $object;
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
     * Generates an argument array ready to be used in running the
     * callback from the given DependencyList instance and the
     * {@see $args} class property.
     * 
     * Loops through {@see $args} class property and changes each
     * instance of Reference into an instantiated dependency instance
     * retrieved from the provided DependencyList.
     *
     * @param DependencyList $dependencyList
     * @return array Arguments array ready to be used for running the
     *         callback.
     *
     */
    protected function generateCallbackArguments(DependencyList $dependencyList)
    {
        $callbackArgs = array();
        
        foreach ($this->args as $value)
        {
            if ($value instanceof Reference)
            {
                $callbackArgs[] = $dependencyList->getInstantiatedDependency($value);
                continue;
            }
            
            $callbackArgs[] = $value;
        }
        
        return $callbackArgs;
    }
    
    /**
     * Run the callback using the given arguments.
     *
     * Since call_user_func_array() is a bit slower, we use an ugly
     * switch statement to try to run the callback using variable
     * function.
     * 
     * @param array $args Arguments to be passed to the callback.
     * @return mixed What the callback returns.
     *
     */
    protected function runCallback(array $args)
    {
        $callback = $this->callback;
        $count = count($args);
        
        switch ($count)
        {
            case 0:
                return $callback();
            break;
            case 1:
                return $callback($args[0]);
            break;
            case 2:
                return $callback($args[0], $args[1]);
            break;
            case 3:
                return $callback($args[0], $args[1], $args[2]);
            break;
            case 4:
                return $callback($args[0], $args[1], $args[2], $args[3]);
            break;
            case 5:
                return $callback($args[0], $args[1], $args[2], $args[3], $args[4]);
            break;
            case 6:
                return $callback($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
            break;
            case 7:
                return $callback($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
            break;
            case 8:
                return $callback($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
            break;
            case 9:
                return $callback($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]);
            break;
            case 10:
                return $callback($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]);
            break;
        }
        
        return call_user_func_array($callback, $args);
    }
}