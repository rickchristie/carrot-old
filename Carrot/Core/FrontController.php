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
 * instance of Dispatch instead of Response, the front controller
 * will re-dispatch based on the returned dispatch instance.
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
     * Dispatches the request based on the dispatch object provided.
     * 
     * Supports internal redirection. If routine method returns an
     * instance of Dispatch instead of Response, the front controller
     * will re-dispatch based on the returned dispatch object.
     * 
     * @param DependencyInjectionContainer $dic Used to instantiate the routine object.
     * @param Dispatch $dispatch The dispatch object.
     * @return Response
     *
     */
    public function dispatch(Dispatch $dispatch, DependencyInjectionContainer $dic)
    {
        $this->throwExceptionIfDispatchInvalid($dispatch);
        $objectReference = $dispatch->getObjectReference();
        $routineMethod = $dispatch->getMethodName();
        $routineObject = $dic->getInstance($objectReference);
        $response = call_user_func_array(array($routineObject, $routineMethod), $dispatch->getArguments());
        
        // Run the dispatch method again if
        // internal redirection is called
        if ($response instanceof Dispatch)
        {
            return $this->dispatch($dic, $response);
        }
        
        if (!($response instanceof Response))
        {
            $className = $objectReference->getClassName();
            $methodName = $dispatch->getMethodName();
            throw new RuntimeException("Front controller error in dispatch, the routine method {$className}::{$methodName}() doesn't return an instance of Carrot\Core\Response.");
        }
        
        $response->setDefaultServerProtocol($this->serverProtocol);
        return $response;
    }
    
    /**
     * Validate dispatch instance, throws exception if fails.
     * 
     * Throws RuntimeException if the class or method from the
     * dispatch instance does not exist.
     *
     * @throws RuntimeException
     * @param Dispatch $dispatch Dispatch instance to be validated.
     *
     */
    protected function throwExceptionIfDispatchInvalid(Dispatch $dispatch)
    {
        $objectReference = $dispatch->getObjectReference();
        $className = $objectReference->getClassName();
        $methodName = $dispatch->getMethodName();
        
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