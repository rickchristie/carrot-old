<?php

namespace Carrot\Autopilot\Instantiator\Rulebook;

use Carrot\Autopilot\Reference,
    Carrot\Autopilot\Instantiator\InstantiatorInterface;

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
     * Should return an instance of InstantiatorInterface or FALSE
     * if the rulebook can't get it.
     * 
     * @param Reference $reference
     * @return InstantiatorInterface|FALSE
     *
     */
    public function getInstantiator(Reference $reference);
}