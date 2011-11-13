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
 * Faux injector.
 *
 * Does not actually instantiate/wire the dependencies of an
 * object. This 'injector' just returns an already instantiated
 * object given at {@see __construct()} when {@see inject()} is
 * called. Since there is only one instance to be returned, the
 * Reference instance that refers to the instance this object is
 * supposed to instantiate must have the singleton lifecycle.
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\DependencyInjection\Injector;

use InvalidArgumentException,
    Carrot\DependencyInjection\Reference,
    Carrot\DependencyInjection\DependencyList;

class FauxInjector implements InjectorInterface
{
    /**
     * @var mixed The object instance to be returned when
     *      {@see inject()} is called.
     */
    protected $object;
    
    /**
     * @var Reference Refers to the object this injector is supposed
     *      to instantiate.
     */
    protected $reference;
    
    /**
     * Constructor.
     * 
     * Pass the object you wanted to be returned immediately:
     *
     * <code>
     * $injector = new FauxInjector(
     *     new Reference('Carrot\Request'),
     *     $request
     * );
     * </code>
     *
     * The container will then get the instance given here directly
     * when it calls {@see inject()}.
     * 
     * @throws RuntimeException If the given Reference instance's
     *         lifecycle is not a singleton.
     * @param Reference $reference Refers to the object this injector
     *        is supposed to instantiate.
     * @param mixed $object The object instance to be returned when
     *        {@see inject()} is called.
     *
     */
    public function __construct(Reference $reference, $object)
    {
        if ($reference->isSingleton() == FALSE)
        {
            $id = $reference->getID();
            throw new InvalidArgumentException("FauxInjector error in instantiation. The given Reference instance's ({$id}) lifecycle is not singleton.");
        }
        
        $this->reference = $reference;
        $this->object = $object;
    }
    
    /**
     * Get the list of dependencies needed to perform instantiation.
     *
     * Since this is a faux injector, the dependency list returned is
     * always an empty one.
     * 
     * @see DependencyList
     * @return DependencyList
     *
     */
    public function getDependencyList()
    {
        return new DependencyList(array());
    }
    
    /**
     * Wire the dependencies provided to the object, returning the
     * instantiated object.
     * 
     * Instantly returns the {@see $object} class property, given
     * in this injector's construction.
     * 
     * @param DependencyList $dependencyList The dependency list of
     *        this class, with all dependencies fulfilled.
     * @return mixed The object that this injector is supposed to
     *         instantiate.
     *
     */
    public function inject(DependencyList $dependencyList)
    {
        return $this->object;
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