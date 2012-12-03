<?php

namespace Carrot\Autopilot\Instantiator;

use Carrot\Autopilot\DependencyList;

/**
 * Interface for instantiators.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
interface InstantiatorInterface
{
    /**
     * Returns the dependency list needed to run the setters.
     * 
     * @return DependencyList
     *
     */
    public function getDependencyList();
    
    /**
     * Returns the Identifier instance of the object this
     * instantiator is supposed to instantiate.
     * 
     * @return Identifier
     *
     */
    public function getIdentifier();
    
    /**
     * Returns TRUE if the dependencies has been fulfilled.
     * 
     * @return bool
     *
     */
    public function isReadyForInjection();
    
    /**
     * Runs the instantiation process.
     * 
     * @return mixed
     *
     */
    public function instantiate();
}