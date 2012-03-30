<?php

namespace Carrot\Autopilot\Instantiator;

/**
 * Instantiators main role is instantiating objects and holding
 * the list of dependencies needed to do that. The Container
 * will inquire it for the list of dependencies that is needed,
 * make sure all dependencies are instantiated, and then tell
 * the instantiator to go and make the object. Each Autopilot
 * reference can only have one instantiator bound to it.
 *
 */
interface InstantiatorInterface
{
    
}