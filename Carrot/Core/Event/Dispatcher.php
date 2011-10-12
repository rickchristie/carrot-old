<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Event dispatcher.
 *
 * Carrot's default implementation of {@see DispatcherInterface}. 
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Event;

use RuntimeException,
    Carrot\Core\DependencyInjection\Reference,
    Carrot\Core\DependencyInjection\Container;

class Dispatcher implements DispatcherInterface
{
    /**
     * @var Container The main dependency injection container.
     */
    protected $container = NULL;
    
    /**
     * @var array List of event listeners, sorted by the event ID
     *      they are listening to.
     */
    protected $listeners = array();
    
    /**
     * Set the container.
     *
     * @param Container $container
     *
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
    
    /**
     * Add a listener, which consists of a Reference that refers to
     * the listener object, and the method to be called when the
     * given event occurs.
     * 
     * @param string $eventID The ID of the event to listen to.
     * @param Reference $reference Refers to the listener object
     *        instance.
     * @param string $methodName The name of the method to be called
     *        when the event occurs.
     * 
     */
    public function addListener($eventID, Reference $reference, $methodName)
    {
        $this->listeners[$eventID][] = array(
            'reference' => $reference,
            'methodName' => $methodName
        );
    }
    
    /**
     * Notify the listeners that the given event has occured.
     *
     * Loops through event listeners and gets their instance via the
     * container given at {@see setContainer()}, calls the registered
     * method names with the given arguments.
     *
     * Return values from the listener method will not be returned to
     * the object that notifies the listeners. If you want to have
     * some interaction between them, you will have to pass in an
     * object as an argument.
     * 
     * @throws RuntimeException If this method is called before the
     *         Container instance is set using {@see setContainer()}.
     * @param string $eventID The ID of the event that happens.
     * @param array $args Arguments to be passed to the listener
     *        method.
     * 
     */
    public function notifyListeners($eventID, array $args = array())
    {
        if ($this->isContainerSet() == FALSE)
        {
            throw new RuntimeException("Dispatcher error when trying to notify listeners for '{$eventID}'. Dependency injection container is not set up yet.");
        }
        
        if (array_key_exists($eventID, $this->listeners) == FALSE)
        {
            return;
        }
        
        foreach ($this->listeners[$eventID] as $listener)
        {
            $instance = $this->container->get($listener['reference']);
            $callback = array($instance, $listener['methodName']);
            
            if (is_callable($callback) == FALSE)
            {
                $id = $listener['reference']->getID();
                throw new RuntimeException("Dispatcher error when trying to notify listener '{$id}' for the event '{$eventID}'. The method name '{$listener['methodName']}' is not a valid callback.");
            }
            
            $this->runCallback($callback, $args);
        }
    }
    
    /**
     * Checks if the Container instance is already set
     * ({@see setContainer()}) or not.
     *
     * @return bool TRUE if the Container is set and ready for use,
     *         FALSE otherwise.
     *
     */
    protected function isContainerSet()
    {
        return ($this->container instanceof Container);
    }
    
    /**
     * Run the callback given with the given arguments.
     * 
     * Since call_user_func_array() is a bit slower, we use an ugly
     * switch statement to try to run the callback using variable
     * function.
     * 
     * @param array $callback The callback to be run.
     * @param array $args The arguments to be used when running the
     *        callback.
     *
     */
    protected function runCallback(array $callback, array $args)
    {
        $object = $callback[0];
        $method = $callback[1];
        $count = count($args);
        
        switch ($count)
        {
            case 0:
                $object->$method();
                return;
            break;
            case 1:
                $object->$method($args[0]);
                return;
            break;
            case 2:
                $object->$method($args[0], $args[1]);
                return;
            break;
            case 3:
                $object->$method($args[0], $args[1], $args[2]);
                return;
            break;
            case 4:
                $object->$method($args[0], $args[1], $args[2], $args[3]);
                return;
            break;
            case 5:
                $object->$method($args[0], $args[1], $args[2], $args[3], $args[4]);
                return;
            break;
            case 6:
                $object->$method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
                return;
            break;
            case 7:
                $object->$method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
                return;
            break;
            case 8:
                $object->$method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
                return;
            break;
            case 9:
                $object->$method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]);
                return;
            break;
            case 10:
                $object->$method($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]);
                return;
            break;
        }
        
        call_user_func_array($callback, $args);
        return;
    }
}