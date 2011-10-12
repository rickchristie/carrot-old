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
 * Injector interface.
 *
 * The injector's responsibility is to hold information on the
 * dependencies and how to wire them to instantiate the needed
 * object. The injector knows how to create only one specific
 * object instance, which means each injector is tied to an
 * instance ID, represented by a Reference instance.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection\Injector;

use Carrot\Core\DependencyInjection\DependencyList,
    Carrot\Core\DependencyInjection\Reference;

interface InjectorInterface
{
    /**
     * Get the list of dependencies needed to perform instantiation.
     *
     * The Container will call this method to get information on
     * which object dependencies it needs to instantiate in order to
     * use the injector. This method will return an instance of
     * DependencyList, which will then be read and filled by the
     * container with instantiated dependencies.
     *
     * The dependencies in the list here are only dependencies to
     * another object. Dependencies to primitive types like array or
     * string does not have recursive dependency and therefore can
     * be resolved by the injector alone.
     *
     * If the injector doesn't have any object dependency, simply
     * create an instance of DependencyList with an empty array as
     * the argument and return it.
     * 
     * @see DependencyList
     * @return DependencyList
     *
     */
    public function getDependencyList();
    
    /**
     * Wire the dependencies provided to the object, returning the
     * instantiated object.
     * 
     * The DependencyList instance provided is the same instance that
     * the container got from {@see getDependencyList()}, with all
     * dependencies fulfilled. If the value returned from this method
     * is not an instance of the class the injector is supposed to
     * instantiate (based on {@see Reference::getClass()}), the
     * container will throw an exception.
     * 
     * @param DependencyList $dependencyList The dependency list of
     *        this class, with all dependencies fulfilled.
     * @return mixed The object that this injector is supposed to
     *         instantiate.
     *
     */
    public function inject(DependencyList $dependencyList);
    
    /**
     * Get the reference for the instance this injector is supposed
     * to instantiate.
     * 
     * @return Reference
     *
     */
    public function getReference();
}