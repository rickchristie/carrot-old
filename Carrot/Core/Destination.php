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
 * Destination
 * 
 * Value object, represents a Destination, namely, the object
 * reference for the routine object (used by the DIC for
 * instantiation), the routine method to call and the arguments to
 * pass to the method. Returned by Router class, used by the
 * FrontController.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use RuntimeException;

class Destination
{
    /**
     * @var string The object reference to the routine class.
     */
    protected $objectReference;
    
    /**
     * @var string Method name to be called.
     */
    protected $routineMethodName;
    
    /**
     * @var array Array of arguments to be passed to the routine method.
     */
    protected $arguments;
    
    /**
     * Creates a Destination object.
     *
     * Example usage:
     *
     * <code>
     * $destination = new Destination(
     *     new ObjectReference('Sample\Welcome{Main:Transient}'),
     *     'welcome',
     *     array(5, 'Foo', 'Bar')
     * );
     * </code>
     *
     * @param string $instanceName The instance name of the routine object.
     * @param string $routineMethodName The name of the routine method.
     * @param array $params Array of parameters, to be passed in sequence.
     *
     */
    public function __construct(ObjectReference $objectReference, $routineMethodName, array $arguments = array())
    {
        $this->objectReference = $objectReference;
        $this->routineMethodName = $routineMethodName;
        $this->arguments = $arguments;
    }
    
    /**
     * Returns the routine object's instance of object reference.
     *
     * @return ObjectReference The routine object's instance of object reference.
     *
     */
    public function getObjectReference()
    {
        return $this->objectReference;
    }
        
    /**
     * Returns the routine method name.
     *
     * @return string Routine method name.
     *
     */
    public function getRoutineMethodName()
    {
        return $this->routineMethodName;
    }
    
    /**
     * Returns arguments to pass to the method.
     *
     * @return array Arguments to be passed to the routine method, sequentially.
     *
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}