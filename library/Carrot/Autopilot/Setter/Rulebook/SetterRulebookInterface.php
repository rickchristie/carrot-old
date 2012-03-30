<?php

namespace Carrot\Autopilot\Setter\Rulebook;

use Carrot\Autopilot\Reference,
    Carrot\Autopilot\Setter\SetterInterface;

/**
 * Each rulebook objects acts as a container for specific rules,
 * which are then consulted by the Autopilot object to get
 * instantiators or setters.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
interface SetterRulebookInterface
{
    /**
     * Try to see if you can get the setter for the given Autopilot
     * reference.
     * 
     * Should return an instance of SetterInterface or FALSE if the
     * rulebook can't get it.
     * 
     * @param Reference $reference
     * @return SetterInterface|FALSE
     *
     */
    public function getSetter(Reference $reference);
}