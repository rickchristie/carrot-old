<?php

namespace Carrot\Autopilot\Setter;

/**
 * Setters main role is to run setter methods after objects are
 * successfully instantiated by the instantiator. Setters hold
 * the list of dependencies needed to perform setter injections.
 * The container will inquire this dependency list from the
 * setter, make sure they are all taken care of, and then tell
 * the setter to run the setter methods. While an Autopilot
 * reference can have just one instantiator, it can have multiple
 * setters.
 *
 */
interface SetterInterface
{
    
}