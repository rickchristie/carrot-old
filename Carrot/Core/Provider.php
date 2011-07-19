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
 * Provider
 * 
 * This provider class implements ProviderInterface and serves as
 * a helper to make creating provider class easier. You may choose
 * to ignore this class altogether and create a new implementation
 * of the ProviderInterface. Example of a provider class extending
 * this base class:
 *
 * <code>
 * namespace Carrot\Database;
 *
 * use Carrot\Core\Provider;
 *
 * class MySQLiProvider extends Provider
 * {
 *     public function getDependencies()
 *     {
 *         return array
 *         (
 *             'config' => new InstanceName('Carrot\Helper\Config@Main:Singleton'),
 *             'logDB' => new InstanceName('Carrot\Database\MySQLi@Logging:Singleton')
 *         );
 *     }
 *
 *     public function getDefault()
 *     {
 *         // You can immediately use the dependencies in your
 *         // provider methods, no need to do anything else.
 *         return new MySQLi
 *         (
 *             $this->config->get('db_hostname'),
 *             $this->config->get('db_database'),
 *             $this->config->get('db_username'),
 *             $this->config->get('db_password')
 *         );
 *     }
 * }
 * </code>
 *
 * For more information, please see the docs for
 * {@see Carrot\Core\Interfaces\ProviderInterface} and
 * {@see Carrot\Core\DependencyInjectionContainer}.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Carrot\Core\Interfaces\ProviderInterface;

class Provider implements ProviderInterface
{   
    /**
     * Empty constructor.
     *
     * The provider class needs to have a constructor without
     * arguments so that DIC can safely instantiate it.
     *
     */
    public function __construct()
    {
        
    }
    
    /**
     * Get the dependencies in array.
     * 
     * In this method, you have to return an associative array that
     * lists the provider class's dependencies. Example:
     *
     * <code>
     * return array
     * (
     *     'config' => new InstanceName('Carrot\Helper\Config@Main:Singleton'),
     *     'logDB' => new InstanceName('Carrot\Database\MySQLi@Logging:Singleton')
     * );
     * </code>
     * 
     * @return array List of dependencies as an array.
     *
     */
    public function getDependencies()
    {
        return array();
    }
}