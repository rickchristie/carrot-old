<?php

namespace Carrot\Autopilot\Instantiator;

/**
 * Generic constructor injector, both used by automatic wiring
 * and manual overrides.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class CtorInjector implements InstantiatorInterface
{
    /**
     * @see getDependencyList()
     * @var DependencyList $list
     */
    private $list;
    
    /**
     * @see getIdentifier()
     * @var Identifier $identifier
     */
    private $identifier;
    
    /**
     * @see __construct()
     * @var array $args
     */
    private $args;
    
    /**
     * Constructor.
     * 
     * @param Identifier $identifier
     * @param DependencyList $list
     * @param array $args
     *
     */
    public function __construct(
        Identifier $identifier,
        DependencyList $list,
        array $args = array()
    )
    {
        $this->identifier = $identifier;
        $this->list = $list;
        $this->args = $args;
    }
    
    /**
     * Returns the dependency list needed to run the setters.
     * 
     * @return DependencyList
     *
     */
    public function getDependencyList()
    {
        return $this->list;
    }
    
    /**
     * Returns TRUE if the dependencies has been fulfilled.
     * 
     * @return bool
     *
     */
    public function isReadyForInjection()
    {
        return $this->list->isFulfilled();
    }
    
    /**
     * Runs the instantiation process.
     * 
     * @return mixed
     *
     */
    public function instantiate()
    {
        
    }
}