<?php

namespace Carrot\Autopilot\Rulebook\Ins;

use Carrot\Autopilot\Reference,
    Carrot\Autopilot\Instantiator;

/**
 * Each rulebook objects acts as a container for specific rules,
 * which are then consulted by the Autopilot object to get
 * either instantiators or setters.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
interface InstantiatorRulebookInterface
{   
    /**
     * Try to see if you can get the instantiator for the given
     * Autopilot reference.
     * 
     * @param Reference $reference
     * @return InstantiatorInterface
     *
     */
    public function getInstantiator(Reference $reference);
}