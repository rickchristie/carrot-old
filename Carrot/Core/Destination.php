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
 * Value object, represents a Destination, namely, the routine
 * object's instance name (used by the DIC for instantiation),
 * the method to call and the arguments to pass to the method.
 * Returned by Router class, used by the FrontController.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class Destination
{
    /**
     * @var string Routine object's instance name, consists of fully qualified class name and a configuration name.
     */
    protected $instanceName;
    
    /**
     * @var string Method name to be called.
     */
    protected $routineMethodName;
    
    /**
     * @var array Array of arguments to be passed to the routine method.
     */
    protected $arguments;
    
    /**
     * @var string Fully qualified class name extracted from the instance name.
     */
    protected $routineClassName;
    
    /**
     * Creates a Destination object.
     *
     * Will throw an exception if the routine object's instance name
     * doesn't pass validation process. Example object construction:
     *
     * <code>
     * $destination = new Destination
     * (
     *     '\Vendor\Namespace\Subnamespace\BlogController@Main',
     *     'index',
     *     array(5, 'Foo', 'Bar')
     * );
     * </code>
     *
     * @param string $instanceName The instance name of the routine object.
     * @param string $routineMethodName The name of the routine method.
     * @param array $params Array of parameters, to be passed in sequence.
     *
     */
    public function __construct($instanceName, $routineMethodName, array $arguments = array())
    {
        $instanceName = $this->validateInstanceName($instanceName);
        $this->instanceName = $instanceName;
        $this->routineMethodName = $routineMethodName;
        $this->arguments = $arguments;
        $this->routineClassName = $this->extractClassName($instanceName);
    }
    
    /**
     * Returns the routine object's instance Name.
     *
     * @return string Routine object's instance name.
     *
     */
    public function getInstanceName()
    {
        return $this->instanceName;
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
    
    /**
     * Returns the routine object's fully qualified class name.
     *
     * @return string The routine object's fully qualified class name (with backslash prefix).
     *
     */
    public function getRoutineClassName()
    {
        return '\\' . $this->routineClassName;
    }
    
    /**
     * Throws exception if instance name is invalid.
     * 
     * Checks that the instance name has the '@' character as the
     * separator between class name and configuration name. Also
     * makes sure that both are not empty.
     * 
     * @throws \RuntimeException
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
            throw new \RuntimeException("Error in creating a Destination object, '{$instanceName}' is not a valid instance name.");
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
}