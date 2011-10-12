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
 * Event dispatcher interface.
 *
 * This interface defines the contract between the event
 * dispatcher with Carrot's core classes. The event dispatcher is
 * a simple way to allow hook user's code into Carrot's core
 * without modifying any file.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Event;

use Carrot\Core\DependencyInjection\Reference,
    Carrot\Core\DependencyInjection\Container;

interface DispatcherInterface
{
    /**
     * Set the container.
     *
     * \Carrot\Core\System will call this method and pass the main
     * dependency injection container after loading the event
     * dispatcher configuration file. The dispatcher will need the
     * container to get instances of listener objects when an event
     * occurs.
     *
     * @param Container $container
     *
     */
    public function setContainer(Container $container);
    
    /**
     * Add a listener, which consists of a Reference that refers to
     * the listener object, and the method to be called when the
     * given event occurs.
     *
     * Each event is identified by an event ID string and can have
     * as many listeners as needed. When notifying that an event has
     * happened ({@see notifyListeners()}), all listeners to the
     * particular event are notified.
     *
     * See the documentation for the list of Carrot's core events.
     * 
     * @param string $eventID The ID of the event to listen to.
     * @param Reference $reference Refers to the listener object
     *        instance.
     * @param string $methodName The name of the method to be called
     *        when the event occurs.
     * 
     */
    public function addListener($eventID, Reference $reference, $methodName);
    
    /**
     * Notify the listeners that the given event has occured.
     * 
     * As a convention, listener methods are to be called in the
     * order of their registration. Listeners that were registered
     * first gets notified first.
     *
     * This method should loop through event listeners and get their
     * instance via the container given at {@see setContainer()}, the
     * arguments should be passed as if the argument array were
     * passed through call_user_func_array().
     *
     * Return values from the listener method will not be returned to
     * the object that notifies the listeners. If you want to have
     * some interaction between them, you will have to pass in an
     * object as an argument.
     * 
     * @param string $eventID The ID of the event that happens.
     * @param array $args Arguments to be passed to the listener
     *        method.
     * 
     */
    public function notifyListeners($eventID, array $args = array());
}