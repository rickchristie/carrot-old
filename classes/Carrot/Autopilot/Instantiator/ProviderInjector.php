<?php

namespace Carrot\Autopilot\Instantiator;

use ReflectionClass,
    Carrot\Autopilot\Identifier,
    Carrot\Autopilot\DependencyList;

/**
 * Generic provider injector, used for provider based manual
 * override.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class ProviderInjector implements InstantiatorInterface
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
     * @see getProviderIdentifier()
     * @var Identifier $providerIdentifier
     */
    private $providerIdentifier;
    
    /**
     * @see __construct()
     * @var string $method
     */
    private $method;
    
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
     * @param ProviderIdentifier $providerIdentifier
     * @param string $method
     *
     */
    public function __construct(
        Identifier $identifier,
        DependencyList $list,
        Identifier $providerIdentifier,
        $method,
        array $args = array()
    )
    {
        $this->identifier = $identifier;
        $this->list = $list;
        $this->providerIdentifier = $providerIdentifier;
        $this->method = $method;
        $this->args = $args;
        $this->list->add($this->providerIdentifier);
        
        foreach ($this->args as $value)
        {
            if ($value instanceof Identifier)
            {
                $this->list->add($value);
            }
        }
    }
    
    /**
     * Returns the dependency list needed to instantiate the provider
     * and run the provider method.
     * 
     * @return DependencyList
     *
     */
    public function getDependencyList()
    {
        return $this->list;
    }
    
    /**
     * Returns the Identifier instance of the object this
     * instantiator is supposed to instantiate.
     * 
     * @return Identifier
     *
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
     * Runs the instantiation process. In this case, instantiates
     * the provider and runs the method with the given arguments.
     * 
     * @return mixed
     *
     */
    public function instantiate()
    {
        // Get the provider from the dependency list.
        $provider = $this->list->getObject(
            $this->providerIdentifier->get()
        );
        
        
    }
}