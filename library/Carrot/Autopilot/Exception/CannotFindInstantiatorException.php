<?php

namespace Carrot\Autopilot\Exception;

use RuntimeException;

/**
 * Thrown when autopilot cannot find instantiator for an
 * autopilot reference even after consulting all the rulebooks.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class CannotFindInstantiatorException extends RuntimeException
{
    
}