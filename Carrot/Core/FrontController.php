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
 * Front Controller
 * 
 * The front controller's responsibility is to use RouterInterface
 * to get the destination instance. It then uses information from
 * the destination instance to instantiate the routine object
 * (using DIC) and then calls the routine method along with the
 * given arguments.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use RuntimeException;

class FrontController
{
    /**
     * @var string The server protocol currently used by the server.
     */
    protected $serverProtocol;
    
    /**
     * @var bool If true, front controller will override response object's server protocol with its own.
     */
    protected $overrideResponseProtocol;
    
    /**
     * Constructs the front controller.
     * 
     * 
     * 
     * @param string $serverProtocol The server protocol currently used by the server.
     * @param bool $overrideResponseProtocol If true, front controller will override response object's server protocol with its own.
     *
     */
    public function __construct($serverProtocol, $overrideResponseProtocol = true)
    {
        $this->serverProtocol = $serverProtocol;
        $this->overrideResponseProtocol = $overrideResponseProtocol;
    }
    
    /**
     * Dispatches the request based on the destination object provided.
     * 
     * 
     * 
     * @param DependencyInjectionContainer $dic Used to instantiate the routine object.
     * @param Destination $destination The destination to dispatch.
     * @return Response
     *
     */
    public function dispatch(DependencyInjectionContainer $dic, Destination $destination)
    {
        $this->throwExceptionIfDestinationInvalid($destination);
        $objectReference = $destination->getObjectReference();
        $routineMethod = $destination->getRoutineMethodName();
        $routineObject = $dic->getInstance($objectReference);
        $response = call_user_func_array(array($routineObject, $destination->getRoutineMethodName()), $destination->getArguments());
        
        // Run the dispatch method again if
        // internal redirection is called
        if ($response instanceof Destination)
        {
            return $this->dispatch($dic, $response);
        }
        
        if (!($response instanceof Response))
        {
            $className = $objectReference->getClassName();
            $methodName = $destination->getRoutineMethodName();
            throw new RuntimeException("Front controller error in dispatch, the routine method {$className}::{$methodName}() doesn't return an instance of Carrot\Core\Response.");
        }
        
        if ($this->overrideResponseProtocol)
        {
            $response->setProtocol($this->serverProtocol);
        }
        
        return $response;
    }
    
    /**
     * Validate destination, throws exception if fails.
     * 
     * Throws RuntimeException if the routine class or routine method
     * does not exist.
     *
     * @throws RuntimeException
     * @param Destination $destination Destination instance to be validated.
     *
     */
    protected function throwExceptionIfDestinationInvalid(Destination $destination)
    {
        $objectReference = $destination->getObjectReference();
        $className = $objectReference->getClassName();
        $methodName = $destination->getRoutineMethodName();
        
        if (!class_exists($className))
        {
            throw new RuntimeException("Front controller error, cannot run routine method {$className}::{$methodName}(). Routine class does not exist.");
        }
        
        if (!method_exists($className, $methodName))
        {
            throw new RuntimeException("Front controller error, cannot run routine method {$className}::{$methodName}(). Method does not exist.");
        }
    }
    
}