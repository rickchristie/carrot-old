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
 * Callback
 *
 * Value object, represents a reference to an executable piece
 * of code inside an object method. This is not a usual callback,
 * however, since it uses DependencyInjectionContainer to get the
 * instance of the object beforehand.
 *
 * As a callback object, it containes an ObjectReference instance
 * that refer to the object instance that houses the method, the
 * method name, and arguments to pass when calling the method.
 * Core classes use this object to run a subroutine defined in a
 * higher-level layer, which is the layer that contains your
 * classes.
 *
 * This object is used by Carrot to run your routine method,
 * hence why your route objects must return an instance of this
 * class if it chooses to do the routing.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use RuntimeException;

class Callback
{
    /**
     * @var string The object reference to be instantiated.
     */
    protected $objectReference;
    
    /**
     * @var string Method name to be called.
     */
    protected $methodName;
    
    /**
     * @var array Array of arguments to be passed to the method.
     */
    protected $arguments;
    
    /**
     * Constructor.
     *
     * Example usage:
     *
     * <code>
     * $callback = new Callback(
     *     new ObjectReference('Sample\Welcome{Main:Transient}'),
     *     'welcome',
     *     array(5, 'Foo', 'Bar')
     * );
     * </code>
     *
     * @param string $instanceName The instance name of the object.
     * @param string $methodName The name of the method to be called.
     * @param array $params Array of parameters, to be passed in sequence.
     *
     */
    public function __construct(ObjectReference $objectReference, $methodName, array $arguments = array())
    {
        $this->objectReference = $objectReference;
        $this->methodName = $methodName;
        $this->arguments = $arguments;
    }
    
    /**
     * Run the callback.
     * 
     * Uses the DIC to get an instance of the object that contained
     * the callback method, and after checking if the object and
     * method is callable, proceeds to call the method with 
     * call_user_func_array().
     *
     * Throws RuntimeException if the object method is not callable.
     *
     * @throws RuntimeException
     * @param DependencyInjectionContainer $dic Used to get the instance of the callback object.
     *
     */
    public function run(DependencyInjectionContainer $dic)
    {
        $object = $dic->getInstance($this->objectReference);
        
        if (!is_callable(array($object, $this->methodName)))
        {
            $className = $this->objectReference->getClassName();
            throw new RuntimeException("Callback error in trying to call method. {$className}::{$this->methodName} is not callable.");
        }
        
        return call_user_func_array(array($object, $this->methodName), $this->arguments);
    }
    
    /**
     * Returns the object reference.
     *
     * @return ObjectReference The object reference to be instantiated.
     *
     */
    public function getObjectReference()
    {
        return $this->objectReference;
    }
        
    /**
     * Returns the method name.
     *
     * @return string Method name to be called.
     *
     */
    public function getMethodName()
    {
        return $this->methodName;
    }
    
    /**
     * Returns arguments to pass to the method.
     *
     * @return array Arguments to be passed to the method, sequentially.
     *
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}