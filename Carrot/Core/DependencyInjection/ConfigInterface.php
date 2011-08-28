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
 * Dependency Injector Configuration Interface
 * 
// ---------------------------------------------------------------
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection;

interface ConfigInterface
{
    /**
     * Used to mark a configuration array as a constructor arguments binding.
     */
    const CONSTRUCTOR_ARGS = 1;
    
    /**
     * Used to mark a configuration array as a provider class binding.
     */
    const PROVIDER_CLASS = 2;
    
    /**
     * Used to mark a configuration array as a provider callback binding.
     */
    const PROVIDER_CALLBACK = 3;
    
    /**
     * Used to denote that the lifecycle is singleton.
     */
    const SINGLETON = 'S';
    
    /**
     * Used to denote that the lifecycle is transient.
     */
    const TRANSIENT = 'T';
    
    /**
     * Get the configuration array.
     * 
    // ---------------------------------------------------------------
     * There are three types of dependency injection configuration
     * that the Injector understands: constructor arguments, provider
     * class, and provider callback.
     * 
     * TODO: Intro on constructor arguments configuration
     * 
     * Example of constructor arguments configuration:
     * 
     * <code>
     * $config = array(
     *     'type' => self::CONSTRUCTOR_ARGS,
     *     ''
     * );
     * </code>
     * 
     * @param string $className
     * @param string $configName
     * @param string $lifecycle
     * 
     */
    public function get($className, $configName, $lifecycle);
}