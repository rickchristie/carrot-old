<?php

namespace Carrot\Autopilot\Exception;

use RuntimeException;

/**
 * Thrown when setter and instantiators are told to run when
 * their dependencies has not been resolved yet.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class UnresolvedDependencyException extends RuntimeException
{
    
}