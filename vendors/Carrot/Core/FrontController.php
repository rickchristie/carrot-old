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
 * The front controller's responsibility is to use the instance of RouterInterface
 * 
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class FrontController
{
    /**
     * @var RouterInterface Instance of an implementation of RouterInterface.
     */
    protected $router;
    
    /**
     * @var ErrorHandlerInterface Instance of an implementation of ErrorHandlerInterface.
     */
    protected $error_handler;
    
    /**
     * @var DependencyInjectionContainer Instance of Carrot's dependency injection container.
     */
    protected $dic;
    
    /**
     * @var string List of Destination that this class has processed in string.
     */
    protected $destination_history = 'Destination history:';
    
    /**
     * @var int Count of how many internal redirection is done in dispatch.
     */
    protected $destination_count = 0;
    
    /**
     * Constructs the FrontController.
     * 
     * 
     * 
     * @param RouterInterface $router Instance of an implementation of RouterInterface.
     * @param ErrorHandlerInterface $error_handler Instance of an implementation of ErrorHandlerInterface.
     * @param DependencyInjectionContainer $dic Carrot's default dependency injection container.
     * @param string $routes_file_absolute_path Absolute path to the file that contain the routes.
     *
     */
    public function __construct(Interfaces\RouterInterface $router, Interfaces\ErrorHandlerInterface $error_handler, DependencyInjectionContainer $dic, $routes_file_absolute_path)
    {
        $error_handler->set();
        $router->loadRoutesFile($routes_file_absolute_path);
        $this->router = $router;
        $this->error_handler = $error_handler;
        $this->dic = $dic;
    }
    
    /**
     * Dispatches the request to the controller to get a response in return.
     *
     * Throws RuntimeException when there is a problem with the Router or the return
     * value of the user's controller.
     * 
     * @return ReponseInterface
     *
     */
    public function dispatch()
    {
        $destination = $this->router->getDestination();
        $this->throwExceptionIfNotDestination($destination);
        $response = $this->getResponse($destination);
        
        // If not an instance of ResponseInterface, throw exception.
        if (!is_a($response, '\Carrot\Core\Interfaces\ResponseInterface'))
        {
            if (is_object($response))
            {
                $type = get_class($response);
            }
            else
            {
                $type = gettype($response);
            }
            
            throw new \RuntimeException("Front controller error, expected \Carrot\Core\Interfaces\ResponseInterface from user controller, got '{$type}' instead. {$this->destination_history}.");
        }
        
        return $response;
    }
    
    // ---------------------------------------------------------------
    
    /**
     * Gets the response from a destination.
     *
     * Switches to get the no-matching-route destination instead if controller class
     * does not exist or the the method is not callable. 
     *
     * @throws RuntimeException
     *
     */
    protected function getResponse(Destination $destination)
    {
        $this->logDestinationHistory($destination);
        
        // To prevent infinite loops and/or feature abuse, we limit
        // internal redirections to no more than 10 times.
        
        if ($this->destination_count > 10)
        {
            throw new \RuntimeException("Front controller error, too many internal redirections, possibly an infinite loop. {$this->destination_history}.");
        }
        
        if (!class_exists($destination->getClassName()))
        {
            return $this->getNoMatchingRouteResponse();
        }
        
        $controller = $this->dic->getInstance($destination->getControllerDICItemID());
        
        if (!is_callable(array($controller, $destination->getMethodName())))
        {
            return $this->getNoMatchingRouteResponse();
        }
        
        $response = call_user_func_array(array($controller, $destination->getMethodName()), $destination->getParams());
        
        // If the response is an instance of Destination, then the controller
        // is signalling an internal redirection, do a recursive call.
        
        if (is_a($response, '\Carrot\Core\Destination'))
        {
            return $this->getResponse($response);
        }
        
        return $response;
    }
    
    /**
     * Gets the no-matching-route destination response.
     *
     * Gets the destination via RouterInterface::getDestinationForNoMatchingRoute().
     * Throws RuntimeException if the controller class does not exist or the method
     * is not callable.
     * 
     * @return mixed Returns whatever the no-matching-route controller method returns.
     * @throws RuntimeException
     *
     */
    protected function getNoMatchingRouteResponse()
    {   
        $destination = $this->router->getDestinationForNoMatchingRoute();
        $this->throwExceptionIfNotDestination($destination);
        $this->logDestinationHistory($destination);
        
        if (!class_exists($destination->getClassName()))
        {
            throw new \RuntimeException("Front controller error when attempting to use 'no-matching-route' destination, class does not exist ({$class_name}). {$this->destination_history}.");
        }
        
        $controller = $this->dic->getInstance($destination->getControllerDICItemID());
        
        if (!is_callable(array($controller, $destination->getMethodName())))
        {
            throw new \RuntimeException("Front controller error when attempting to use 'no-matching-route' destination, method does not exist ({$destination->getClassName()}::{$destination->getMethodName()}). {$this->destination_history}.");
        }
        
        return call_user_func_array(array($controller, $destination->getMethodName()), $destination->getParams());
    }
    
    protected function logDestinationHistory(Destination $destination)
    {
        $this->destination_count++;
        $this->destination_history .= " ({$this->destination_count}. {$destination->getControllerDICItemID()}::{$destination->getMethodName()})";
    }
    
    /**
     * Checks if the provided variable is an instance of Destination.
     *
     * Throws a RuntimeException when the given variable is not a valid
     * instance of \Carrot\Core\Destination.
     * 
     * @param mixed $destination
     * @throws RuntimeException
     *
     */
    protected function throwExceptionIfNotDestination($destination)
    {
        if (!is_a($destination, '\Carrot\Core\Destination'))
        {
            if (is_object($destination))
            {
                $type = get_class($destination);
            }
            else
            {
                $type = gettype($destination);
            }
            
            // In case we're getting this straight from RouterInterface::getDestination()
            if (empty($this->destination_history))
            {
                $this->destination_history = ' (Null)';
            }
            
            throw new \RuntimeException("Front controller error, expected an instance of \Carrot\Core\Destination from Router, got {$type} instead. {$this->destination_history}");
        }
    }
}