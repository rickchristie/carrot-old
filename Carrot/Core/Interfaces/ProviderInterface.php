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
     * Forces the constructor to be a leaf method.
     * 
     * Dependencies that the provider class has will be injected
     * using setter injection instead.
     *
     */
    public function __construct();
    
    /**
     * Must return a dependency list object.
     * 
     * Your provider class might have dependencies of their own. This
     * method must return the said dependencies in an array form that
     * contains the name of the dependency and the instance name, for
     * example:
     *
     * <code>
     * return array
     * (
     *     'config' => 'Carrot\Helper\Config@Database',
     *     'mysqli' => 'Carrot\Database\MySQLi@Backup'
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
    
    /**
     * Set the dependencies.
     * 
     * The DIC class will call this method with an array populated
     * with the dependencies you need. DIC will throw an exception if
     * it fails to load one of the dependencies, so you can be sure
     * that the array will be populated as you have returned in
     * getDependencies() method.
     * 
     * <code>
     * $provider->setDependencies(array
     * (
     *     'config' => $config,
     *     'mysqli' => $mysqli
     * ));
     * </code>
     * 
     * @param array $dependencies Array of objects.
     * 
     */
    public function setDependencies(array $dependencies);
    
    /**
     * Find out if a particular configuration has a singleton lifecycle.
     * 
     * There are two types of lifecycle that the DIC supports, one is
     * transient, the other is singleton (not to be confused with the
     * Singleton design pattern).
     *
     * Objects with transient lifecycle will be created each time it
     * is needed, while objects with singleton lifecycle will be
     * created only once and shared throughout the request.
     * 
     * The DIC class will call this method to determine if an
     * instance name should have a singleton lifecycle:
     * 
     * <code>
     * $mysqliProvider->isSingleton('Backup');
     * </code>
     * 
     * @param string $configName The configuration name to be asked.
     * @return bool True if singleton, false if transient.
     *
     */
    public function isSingleton($configName);
}