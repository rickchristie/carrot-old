<?php

namespace Carrot\Autopilot\Exception;

use RuntimeException;

/**
 * Thrown when there is a circular dependency on the container's
 * stack.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class CircularDependencyException extends RuntimeException
{
    
}