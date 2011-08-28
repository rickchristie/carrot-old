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
 * Dependency Injector Reference
 * 
// ---------------------------------------------------------------
 * This object represents a reference to a dependency injector
 * configuration for a particular class. Other than used to 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection;

use InvalidArgumentException;

class Reference
{    
    /**
     * @var string The name of the class, fully qualified without backslash prefix.
     */
    protected $className;
    
    /**
     * @var string The name of the dependency injector configuration used to instantiate the class.
     */
    protected $configName;
    
    /**
     * @var string The lifecycle setting string of the reference.
     */
    protected $lifecycle;
    
    /**
     * @var array Contains allowed lifecyle setting strings.
     */
    protected $allowedLifecycles = array('Singleton', 'Transient');
    
    /**
     * Constructor.
     * 
    // ---------------------------------------------------------------
     * Pass the class name, the configuration name, and the lifecycle
     * setting of the 
     * 
     * <code>
     * $reference = new DIReference(
     *     'App\Controller\BlogController',
     *     'Main',
     *     'singleton'
     * );
     * </code>
     * 
     * @param string $className The name of the class, fully qualified without backslash prefix.
     * @param string $configName The name of the dependency injector configuration used to instantiate the class.
     * @param string $lifecycle The lifecycle setting, use one of the provided constants.
     *
     */
    public function __construct($className, $configName, $lifecycle)
    {
        $className = ltrim($className, '\\');
        $this->className = $className;
        $this->configName = (string) $configName;
        
        if (!in_array($lifecycle, $this->allowedLifecycles))
        {
            throw new InvalidArgumentException("DIReference error in instantiation. Unknown lifecycle setting '{$lifecycle}'. Lifecycle setting must be either 'Transient' or 'Singleton'.");
        }
        
        $this->lifecycle = (string) $lifecycle;
    }
    
    /**
     * Get the fully qualified class name.
     *
     * @return string Fully qualified class name (without backslash prefix).
     *
     */
    public function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Get the configuration name.
     *
     * @return string The configuration name.
     *
     */
    public function getConfigName()
    {
        return $this->configName;
    }
    
    /**
     * Get the lifecycle setting.
     * 
     * @return string The lifecycle setting.
     * 
     */
    public function getLifecycle()
    {
        return $this->lifecycle;
    }
}