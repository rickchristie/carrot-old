<?php

namespace Carrot\Autopilot\Setter;

use Carrot\Autopilot\DependencyList;

/**
 * Interface for setter injectors.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
interface SetterInterface
{
    /**
     * Returns the dependency list needed to run the setters.
     * 
     * @return DependencyList
     *
     */
    public function getDependencyList();
    
    /**
     * Get the identifier of the object to be setter-injected by
     * this class.
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
     * Runs the setter injection process.
     * 
     * @param mixed $object
     *
     */
    public function inject($object);
}