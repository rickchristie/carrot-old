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
 * Dispatch
 * 
 * Value object, represents a dispatch message, namely, the object
 * reference (used by the DIC for instantiation), the method to
 * call and the arguments to pass to the method. This object
 * gives the core classes enough information to determine which
 * user code it should invoke.
 *
 * Main usage of this class is to determine which routine object
 * gets initialized, which routine method gets called, and what
 * arguments to be passed to the method. Hence this object is to
 * be returned by the Router class when routing and used by the
 * front controller for dispatching.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use RuntimeException;

class Dispatch
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
     * Creates a dispatch object.
     *
     * Example usage:
     *
     * <code>
     * $dispatch = new Dispatch(
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