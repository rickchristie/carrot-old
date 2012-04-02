<?php

namespace Carrot\Autopilot\Instantiator;

use Carrot\Autopilot\AutopilotLog,
    Carrot\Autopilot\DependencyList,
    Carrot\Autopilot\Reference,
    Carrot\Autopilot\Exception\UnableToInstantiateException,
    Carrot\Autopilot\Exception\UnresolvedDependencyException;

/**
 * Instantiators main role is instantiating objects and holding
 * the list of dependencies needed to do that. The Container
 * will inquire it for the list of dependencies that is needed,
 * make sure all dependencies are instantiated, and then tell
 * the instantiator to go and make the object. Each Autopilot
 * reference can only have one instantiator bound to it.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
interface InstantiatorInterface
{
    /**
     * Gets the object that has the list of dependencies.
     * 
     * The container will instantiate everything in the dependency
     * list recursively.
     * 
     * @return DependencyList
     *
     */
    public function getDependencyList();
    
    /**
     * Gets the autopilot reference to the instance that this
     * instantiator is obliged to instantiate.
     * 
     * @return Reference
     *
     */
    public function getReference();
    
    /**
     * Instantiate the object and return it.
     * 
     * @throws UnresolvedDependencyException If this method is called
     *         when dependency list is not filled yet.
     * @return mixed
     *
     */
    public function instantiate();
    
    /**
    //---------------------------------------------------------------
     * Logs extra information that this instantiator have.
     * 
     * This method will be called 
     * 
     * Try to log information that will be useful for debugging - the
     * list of constructor arguments to use, for example. Use the
     * AutopilotLog::logUsingInstantiator() method so that your
     * message will be correctly grouped.
     * 
     * @param AutopilotLog $log
     *
     */
    public function log(AutopilotLog $log);
}