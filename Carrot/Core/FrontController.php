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
 * Used to dispatch request by instantiating the relevant routine
 * object and calling the appropriate routine method. The routine
 * method must return an instance of Response.
 *
 * Supports internal redirection. If routine method returns an
 * instance of Destination instead of Response, the front
 * controller will re-dispatch to the returned destination.
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
     * Constructs the front controller.
     *
     * Since Response is a value object, it may be created anywhere
     * in the code. However, the response class defaults to HTTP/1.0
     * protocol which the server may not actually use. Example object
     * construction: 
     *
     * <code>
     * $frontController = new FrontController($_SERVER['SERVER_PROTOCOL']);
     * </code>
     * 
     * @param string $serverProtocol The server protocol currently used by the server.
     *
     */
    public function __construct($serverProtocol)
    {
        $this->serverProtocol = $serverProtocol;
    }
    
    /**
     * Dispatches the request based on the destination object provided.
     * 
     * Supports internal redirection. If routine method returns an
     * instance of Destination instead of Response, the front
     * controller will re-dispatch to the returned destination.
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
        
        $response->setDefaultServerProtocol($this->serverProtocol);
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