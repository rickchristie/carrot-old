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
 * Event Dispatcher
 * 
// ---------------------------------------------------------------
 * This class initializes and notifies objects of various core
 * events, allowing custom subroutine to be run when certain event
 * occurs in Carrot's core module. This makes it easier for you to
 * customize 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Exception;
use RuntimeException;

class EventDispatcher
{
    /**
     * @var type comments
     */
    protected $listeners;
    
    /**
     * @var DependencyInjectionContainer comments
     */
    protected $dic;
    
    public function setDIC(DependencyInjectionContainer $dic)
    {
        $this->dic = $dic;
    }
    
    /**
     * Notify
     * 
     * 
     * 
     */
    public function notify($eventName, array $args = array())
    {
        if (!$this->canNotify($eventName))
        {
            return;
        }
        
        foreach ($this->listeners[$eventName] as $index => $listener)
        {
            try
            {
                $objectReference = new ObjectReference($listener['instanceName']);
            }
            catch (Exception $e)
            {
                throw new RuntimeException("Events error in notifying listener #{$index} for {$eventName} event. The string '{$listener['instanceName']}' is not a valid instance name.");
            }
            
            $object = $this->dic->getInstance($objectReference);
            $callback = array($object, $listener['methodName']);
            
            if (!is_callable($callback))
            {
                $callbackString = "{$listener['instanceName']}->{$listener['methodName']}";
                throw new RuntimeException("Events error in notifying listener #{$index} for {$eventName} event. The callback '{$callbackString}' is not callable.");
            }
            
            // TODO: Perhaps replace call_user_func_array() with a method with better performance.
            call_user_func_array(array($object, $listener['methodName']), $args);
        }
    }
    
    /**
     * Add listener.
     *
     */
    public function addListener($eventName, $instanceName, $methodName)
    {
        $this->listeners[$eventName][] = array(
            'instanceName' => $instanceName,
            'methodName' => $methodName
        );
    }
    
    protected function canNotify($eventName)
    {
        return (isset($this->listeners[$eventName]) AND $this->dic instanceof DependencyInjectionContainer);
    }
}