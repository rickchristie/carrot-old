<?php

/**
//---------------------------------------------------------------
 * Events in Carrot.
 * 
 * A collection of library classes that is used by the framework
 * and the user to manage event firing and handling.
 * 
 * Using chain of responsibility pattern with simple rules:
 * 
 * - An event can be handled by multiple handlers.
 * - An event can specify a contract for the event data, requiring
 *   those that fire them to provide certain values.
 * - Handlers have first in first run policy.
 * - Handlers can stop the next handler in the chain from running.
 * - Some events can only be fired once, other can be fired
 *   multiple times.
 * 
 * Every method and callbacks that acts as an event handler must
 * accept one parameter, the EventData object. The EventData
 * object is a container object for parameters.
 *
 * Events can be pre-defined, but it is not required. Likewise,
 * when predefining events, user can use EventDataContract, but
 * it is also not necessary.
 *
 */

/**
 * The HandlerList object acts as a container for event handlers,
 * and handles the instantiation and registration of handlers.
 * 
 * There are two HandlerInterface implementation that can be
 * used, the callback handler, which lets the user define an
 * anonymous function on the fly as the handler, or the DIC
 * handler, which lets the user determine the reference ID
 * of the handler object and the method to run.
 *
 */

$handlers->addCallbackHandler(
    'Carrot.Framework:initialized',
    function(EventData $params, EventResult $result)
    {
        // Handles the event
    }
);

$handlers->addCallbackHandler(
    'Carrot.Framework:initialized',
    array($object, 'methodName')
);

$handlers->addCallbackHandler(
    'Carrot.Framework:initialized',
    'functionName'
);

$handlers->addReferenceHandler(
    'Carrot.Framework:initialized',
    new Reference('App\Events\Bootstrap'),
    'handlerMethod'
);

$handlers->addHandler($handler);

/**
 * Triggering simple events are easy. However, to trigger events
 * with data contract, you will have to build the proper EventData
 * object first.
 * 
 * When triggering the event, the event controller will check
 * if the provided EventData instance fills the contract or not.
 * It will throw exception if it doesn't, which means your event
 * handlers does not have to check for the existence of 
 *
 */

// Simple event notification.
$eventController->trigger('Carrot.Framework:initialized');

// Using events to get the page not found response.
$eventData = new EventData;
$eventData->set('response', $default404Response);
$eventData->set('request', $request);
$eventController->trigger(
    'Carrot.Framework:routeNotFound',
    $eventData
);

/**
 * Event data contract.
 * 
 * The contract determines which object is required on the
 * EventData object when triggering an event. For example,
 * running 'Carrot.Framework:routeNotFound' requires a Response
 * object to be on the EventData object with the variable named
 * 'response'.
 * 
 * This helps if you wanted to be strict with your events and
 * is a sort of an implementation of defensive programming by
 * making sure your event handlers always get consistent
 * EventData instances. However, this is not required.
 * 
 * List of require methods:
 * 
 * - requireObject
 * - requireString
 * - requireInt
 * - requireFloat
 * - requireBool
 * 
//---------------------------------------------------------------
 * Once a contract has been set 
 *
 */

$contract = new EventDataContract;
$contract->requireObject(
    'request',
    'Carrot\Framework\Request\Request'
);
$contract->requireObject(
    'response',
    'Carrot\Framework\Response\Http\Response'
);
$eventList->addEvent(
    'Carrot.Framework:routeNotFound',
    $contract,
    TRUE // One time event
);

if ($contract->isFulfilled($eventData))
{
    throw new RuntimeException('...');
}

$eventData->setContract($contract);

/**
 * Events can be determined in advance. This helps if you need
 * a certain contract for the event. However, if the event has
 * the following characteristics:
 * 
 * - Does not have data contract.
 * - Can be triggered multiple times.
 * 
 * You don't have to define it in advance. The controller will
 * create an event for it on the fly when it is triggered.
 *
 */

$eventList->addEvent(
    'Carrot.Framework:responseFilter',
    $eventDataContract,
    TRUE // One time event or not
);