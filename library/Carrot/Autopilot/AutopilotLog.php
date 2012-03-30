<?php

namespace Carrot\Autopilot;

/**
 * Responsible for logging all operations on Autopilot.
 * 
 * Automatic dependency injection, although convenient, will be
 * harder to debug than hard wired dependency injection.
 * Extensive logging is done to make debugging Autopilot
 * rules easier. This log instance can then be printed out or
 * mailed when an exception occurs.
 * 
 * What is being logged when calling the container's get method:
 * 
 * - Which reference is being instantiated by the Container.
 * - Which instantiator rulebook is responsible for creating the
 *   instantiator for the reference.
 * - Which instantiator is being used.
 * - Instantiator dependencies to be instantiated on the stack.
 * - List of setters generated (and from which rulebook).
 * - Setter dependencies to be instantiated on the stack.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class AutopilotLog
{
    
}