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
 * Pointer injector.
 *
 * Instead of instantiating the reference, it instead 'points' to
 * another reference, marking it as a dependency to be resolved.
 * Similar to how pointers points to another memory address. As
 * an example, this injector is used in Carrot to make each
 * unnamed reference to
 * Carrot\Core\ExceptionHandler\HandlerInterface points to the
 * exception handler implementation that the user configured.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection\Injector;

use Carrot\Core\DependencyInjection\Reference,
    Carrot\Core\DependencyInjection\DependencyList;

class PointerInjector implements InjectorInterface
{   
    /**
     * @var Reference Refers to the object this injector is supposed
     *      to instantiate.
     */
    protected $reference;
    
    /**
     * @var Reference The reference that this injector really
     *      instantiates (with the help of the container).
     */
    protected $referredReference;
    
    /**
     * @var DependencyList Will contain only
     *      {@see $referredReference}.
     */
    protected $dependencyList;
    
    /**
     * Constructor.
     * 
     * Pass the reference that this injector points to as the second
     * argument:
     *
     * <code>
     * $injector = new PointerInjector(
     *     new Reference('Carrot\Core\Logbook\LogbookInterface'),
     *     new Reference('Carrot\Core\Logbook\NullLogbook')
     * );
     * </code>
     *
     * With the above injector set, each reference to the logbook
     * interface will instead instantiate Carrot\Core\Logbook. The
     * reference being pointed must be a child of the reference that
     * is acting as a 'pointer', otherwise the container will throw
     * an exception.
     *
     * @param Reference $reference Refers to the object that this
     *        injector is supposed to instantiate.
     * @param Reference $referredReference The reference that this
     *        injector really instantiates (with the help of the
     *        container).
     *
     */
    public function __construct(Reference $reference, Reference $referredReference)
    {
        $this->reference = $reference;
        $this->referredReference = $referredReference;
        $this->dependencyList = new DependencyList(array($referredReference));
    }
    
    /**
     * Get the list of dependencies needed to perform instantiation.
     *
     * Will return a DependencyList instance containing only the
     * referred Reference instance {@see $referredReference}.
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
     * @param DependencyList $dependencyList The dependency list of
     *        this class, with all dependencies fulfilled.
     * @return mixed The object that this injector is supposed to
     *         instantiate.
     *
     */
    public function inject(DependencyList $dependencyList)
    {
        return $dependencyList->getInstantiatedDependency($this->referredReference);
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