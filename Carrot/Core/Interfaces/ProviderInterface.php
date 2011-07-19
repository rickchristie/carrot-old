<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Provider Interface
 * 
 * This is the interface you must implement when you are using
 * Carrot\Core\DependencyInjectionContainer. The provider acts as
 * a small factory, wiring the dependencies and/or configuration
 * by injecting it.
 *
// ---------------------------------------------------------------
 * Providers' constructors must not have any arguments or the DIC
 * will not be able to instantiate it. In an ideal situation, we
 * can use 
 * 
 * This interface defines the contract between your provider
 * classes with Carrot's dependency injection container. In order
 * to make provider class development easier, you can extend
 * Carrot\Core\Provider instead of re-implementing this interface
 * for every provider class.
 *
 * For more information, please see the docs for
 * {@see Carrot\Core\Interfaces\Provider} and
 * {@see Carrot\Core\DependencyInjectionContainer}.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

interface ProviderInterface
{
    /**
     * Must return a dependency list object.
     * 
     * Your provider class might have dependencies of their own. This
     * method must return the said dependencies in an array form that
     * contains the name of the dependency and an instance of
     * Carrot\Core\InstanceName. For example:
     *
     * <code>
     * return array
     * (
     *     'config' => new InstanceName('Carrot\Helper\Config@Database'),
     *     'mysqli' => new InstanceName('Carrot\Database\MySQLi@Backup')
     * );
     * </code>
     *
     * Return an empty array if the provider class has no
     * dependencies. 
     * 
     * @return array List of dependencies and their name.
     *
     */
    public function getDependencies();
}