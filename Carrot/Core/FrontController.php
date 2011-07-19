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
use Carrot\Core\Interfaces\ResponseInterface;
use Carrot\Core\Interfaces\RouterInterface;

class FrontController
{
    /**
     * @var RouterInterface Instance of an implementation of RouterInterface.
     */
    protected $router;
    
    /**
     * Constructs the FrontController.
     *
     * You can replace Carrot's default router class with your own
     * class by implementing the RouterInterface and modifying
     * FrontController's provider class to inject your router class
     * instead.
     * 
     * @param RouterInterface $router Instance of an implementation of RouterInterface.
     *
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    
    /**
     * Dispatches the request by calling the routine object's method.
     *
     * This method first gets the destination from RouterInterface. It
     * then uses the DIC to instantiate the routine object and calling
     * the routine method using call_user_func_array().
     *
     * Throws RuntimeException if the return value from the routine
     * method is not an implementation of ResponseInterface.
     *
     * @throws RuntimeException
     * @param DependencyInjectionContainer $dic Used to instantiate the routine object.
     * @return ResponseInterface
     *
     */
    public function dispatch(DependencyInjectionContainer $dic, Destination $destination = null)
    {
        if (!$destination)
        {
            $destination = $this->router->getDestination();
        }
        
        $this->checkDestination($destination);
        $routineObject = $dic->getInstance($destination->getInstanceName());
        $response = call_user_func_array(array($routineObject, $destination->getRoutineMethodName()), $destination->getArguments());
        
        // Run the dispatch method again if
        // internal redirection is called
        if ($response instanceof Destination)
        {
            return $this->dispatch($dic, $response);
        }
        
        if (!($response instanceof ResponseInterface))
        {
            $className = ltrim($destination->getRoutineClassName(), '\\');
            $methodName = $destination->getRoutineMethodName();
            throw new RuntimeException("Front controller error in dispatch, the routine method {$className}::{$methodName}() doesn't return an implementation of ResponseInterface.");
        }
        
        return $response;
    }
    
    /**
     * Validate destination, throws exception if fails.
     * 
     * Throws RuntimeException if the variable returned by
     * RouterInterface::getDestination() is not an instance of
     * Destination or the routine class or routine method does not
     * exist.
     *
     * @throws RuntimeException
     * @param Destination $destination Destination instance to be validated.
     *
     */
    protected function checkDestination($destination)
    {
        if (!($destination instanceof Destination))
        {
            if (is_object($destination))
            {
                $type = get_class($destination);
            }
            else
            {
                $type = gettype($destination);
            }
            
            $routerClassName = get_class($this->router);
            throw new RuntimeException("Front controller error, expected an instance of Carrot\Core\Destination from {$routerClassName}::getDestination(), got {$type} instead.");
        }
        
        $className = $destination->getRoutineClassName();
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