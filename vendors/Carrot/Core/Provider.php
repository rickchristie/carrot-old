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
 *     protected $dependencies = array
 *     (
 *         'config' => 'Carrot\Helper\Config@Main',
 *         'logDB' => 'Carrot\Database\MySQLi@Logging'
 *     );
 *
 *     protected $singletons = array('Default');
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
     * @var array List of dependencies that this provider has, with variable name as index and instance name as content.
     */
    protected $dependencies = array();
    
    /**
     * @var array List of configuration name that has a singleton lifecycle.
     */
    protected $singletons = array();
    
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
     * Since ProviderInterface::getDependencies() and
     * ProviderInterface::setDependencies() has already been 
     * implemented for you, the only thing you need to do is override
     * the class property $dependencies:
     *
     * <code>
     * namespace Carrot\Database;
     *
     * use Carrot\Core\Provider;
     *
     * class MySQLiProvider extends Provider
     * {
     *     protected $dependencies = array
     *     (
     *         'config' => 'Carrot\Helper\Config@Main',
     *         'logDB' => 'Carrot\Database\MySQLi@Logging'
     *     );
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
     * @return array List of dependencies as an array.
     *
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }
    
    /**
     * Saves each dependency as a class property.
     * 
     * The variable name defined in $dependencies property is used as
     * the property name. For example, if the dependencies array
     * returned from getDependencies() is:
     *
     * <code>
     * $dependencies = array
     * (
     *     'config' => 'Carrot\Helper\Config@Main',
     *     'logDB' => 'Carrot\Database\MySQLi@Logging'
     * );
     * </code>
     *
     * If your provider class is extending this base class, you don't
     * have to do anything. When your provider methods is called by the
     * DIC, this class will already have the following class property:
     *
     * <code>
     * $this->config -> Carrot\Helper\Config@Main object instance
     * $this->logDB -> Carrot\Database\MySQLi@Logging object instance
     * </code>
     * 
     * @param array $dependencies Array containing dependency objects.
     * 
     */
    public function setDependencies(array $dependencies)
    {
        foreach ($dependencies as $variableName => $object)
        {
            $this->$variableName = $object;
        }
    }
    
    /**
     * Checks whether or not the configuration name has a singleton lifecycle.
     * 
     * If you're extending this provider class, in order to define a
     * configuration name to have singleton lifecycle you only have
     * to define them in $singletons class property. Please note that
     * the configuration name is case sensitive.
     *
     * <code>
     * namespace Carrot\Database;
     *
     * use Carrot\Core\Provider;
     *
     * class MySQLiProvider extends Provider
     * {
     *     protected $singleton = array('Default');
     *
     *     public function getDefault()
     *     {
     *         return new MySQLi
     *         (
     *             'localhost',
     *             'username',
     *             'password',
     *             'database'
     *         );
     *     }
     * }
     * </code>
     * 
     * @param string $configName Configuration name to check.
     * @return bool True if it has singleton lifecycle, false if not.
     *
     */
    public function isSingleton($configName)
    {
        if (in_array($configName, $this->singletons))
        {
            return true;
        }
        
        return false;
    }
}