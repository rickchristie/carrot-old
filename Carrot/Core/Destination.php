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
 * Value object, represents a Destination, namely, the
 * controller's instance name (used by the DIC for instantiation),
 * the method to call and the arguments to pass to the method.
 * Returned by Router class, used by the front controller.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class Destination
{
    /**
     * @var string Controller's instance name, consists of fully qualified class name and a configuration name.
     */
    protected $instanceName;
    
    /**
     * @var string Method name to be called.
     */
    protected $methodName;
    
    /**
     * @var array Array of arguments to be passed to the controller method.
     */
    protected $arguments;
    
    /**
     * @var string Fully qualified class name extracted from the instance name.
     */
    protected $className;
    
    /**
     * Creates a Destination object.
     *
     * Will throw an exception if the controller instance name doesn't
     * pass validation process. Example object construction:
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
     * @param string $controller_dic_id DIC item ID for the controller.
     * @param string $method Method name to call.
     * @param array $params Array of parameters, to be passed in sequence.
     *
     */
    public function __construct($instanceName, $methodName, array $arguments = array())
    {
        $instanceName = $this->validateInstanceName($instanceName);
        $this->instanceName = $instanceName;
        $this->methodName = $methodName;
        $this->arguments = $arguments;
        $this->className = $this->extractClassName($instanceName);
    }
    
    /**
     * Returns the controller instance Name.
     *
     * @return string Controller DIC item registration ID.
     *
     */
    public function getInstanceName()
    {
        return $this->instanceName;
    }
    
    /**
     * Returns the method to call from the controller.
     *
     * @return string Method name to call.
     *
     */
    public function getMethodName()
    {
        return $this->methodName;
    }
    
    /**
     * Returns arguments to pass to the method.
     *
     * @return array Arguments to be passed to the controller method, sequentially.
     *
     */
    public function getArguments()
    {
        return $this->arguments;
    }
    
    /**
     * Returns class name.
     *
     * @return string The controller's fully qualified class name (with backslash prefix).
     *
     */
    public function getClassName()
    {
        return '\\' . $this->className;
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