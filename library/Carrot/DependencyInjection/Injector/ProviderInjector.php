<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Provider injector.
 *
 * Wires dependencies using a provider class that encapsulates
 * the object creation logic. The provider class must implement
 * ProviderInterface.
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\DependencyInjection\Injector;

use RuntimeException,
    Carrot\DependencyInjection\Reference,
    Carrot\DependencyInjection\DependencyList;

class ProviderInjector implements InjectorInterface
{
    /**
     * @var Reference Refers to the object this injector is supposed
     *      to instantiate.
     */
    protected $reference;
    
    /**
     * @var Reference Refers to the provider that provides the
     *      instance this injector is supposed to instantiate.
     */
    protected $providerReference;
    
    /**
     * @var DependencyList The dependency list of this injector, it
     *      only contains the {@see $providerReference}.
     */
    protected $dependencyList;
    
    /**
     * Constructor.
     *
     * Pass the reference to the provider in construction:
     *
     * <code>
     * $injector = new ProviderClassInjector(
     *     new Reference('Acme\App\Controller'),
     *     new Reference('Acme\App\ControllerProvider')
     * );
     * </code>
     * 
     * This injector will treat the provider reference as a
     * dependency that needs to be resolved, forcing the container
     * to also recursively instantiate the provider.
     * 
     * @param Reference $reference Refers to the object this injector
     *        is supposed to instantiate.
     * @param Reference $providerReference Refers to the provider
     *        that provides the instance this injector is supposed to
     *        instantiate.
     *
     */
    public function __construct(Reference $reference, Reference $providerReference)
    {
        $this->reference = $reference;
        $this->providerReference = $providerReference;
        $this->dependencyList = new DependencyList(array($providerReference));
    }
    
    /**
     * Get the list of dependencies needed to perform instantiation.
     *
     * Returns the DependencyList instance generated from the
     * arguments provided at object construction.
     * 
     * @see DependencyList
     * @return DependencyList
     *
     */
    public function getDependencyList()
    {
        return $this->dependencyList;
    }
    
    /**
     * Wire the dependencies provided to the object, returning the
     * instantiated object.
     * 
     * Get the provider instance from the dependency list and run the
     * {@see ProviderInterface::get()} method.
     * 
     * @throws RuntimeException When the provider instance doesn't
     *         implement ProviderInterface, also when the provider
     *         doesn't return the appropriate class.
     * @param DependencyList $dependencyList The dependency list of
     *        this class, with all dependencies fulfilled.
     * @return mixed The object that this injector is supposed to
     *         instantiate.
     *
     */
    public function inject(DependencyList $dependencyList)
    {
        $provider = $dependencyList->getInstantiatedDependency($this->providerReference);
        
        if ($provider instanceof ProviderInterface == FALSE)
        {
            $id = $this->reference->getID();
            throw new RuntimeException("ProviderClassInjector error when trying to instantiate '{$id}'. The provider object does not implement Carrot\DependencyInjection\Injector\ProviderInterface.");
        }
        
        $object = $provider->get();
        return $object;
    }
    
    /**
     * Get the reference for the instance this injector is supposed
     * to instantiate.
     * 
     * @return Reference
     *
     */
    public function getReference()
    {
        return $this->reference;
    }
}