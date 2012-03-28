<?php

namespace Carrot\Autopilot;

use RuntimeException,
    InvalidArgumentException;

/**
 * The actual dependency injection container.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Container
{
    /**
     * Gets the instance referred to by the given Autopilot reference.
     * 
     * @throws RuntimeException If the container failed to
     *         instantiate the objects.
     * @throws InvalidArgumentException If the class doesn't exist.
     * @param Reference $reference
     * @return mixed
     *
     */
    public function get(Reference $reference)
    {
        
    }
}