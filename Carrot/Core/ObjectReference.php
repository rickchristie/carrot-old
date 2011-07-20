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
 * Object Reference
 * 
 * Value object, used by DependencyInjectionContainer as an
 * identification for an instance. It is merely a container and
 * validator for the instance name.
 *
 * The instance name contains the fully qualified class name, a
 * configuration name and a lifecycle setting. Here is an example
 * of the format:
 *
 * <code>
 * Carrot\Core\FrontController{Main:Transient}
 * Carrot\Database\MySQLi{Backup:Singleton}
 * </code>
 *
 * The lifecycle setting is part of the instance name, so these
 * two instance names may point to different instantiation
 * configuration:
 *
 * <code>
 * Carrot\Core\FrontController{Main:Transient}
 * Carrot\Core\FrontController{Main:Singleton}
 * </code>
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use InvalidArgumentException;

class ObjectReference
{
    /**
     * @var string The instance name string.
     */
    protected $instanceName;
    
    /**
     * @var string Fully qualified class name, without backslash prefix.
     */
    protected $className;
    
    /**
     * @var string The configuration name string.
     */
    protected $configurationName;
    
    /**
     * @var string The lifecycle setting, could be 'singleton' or 'transient'.
     */
    protected $lifecycleSetting;
    
    /**
     * Constructs the instance name.
     *
     * Trims the instance name for backslashes using ltrim() before
     * validating. Checks the instance name against the pattern (in
     * PHP preg_match()):
     * 
     * <code>
     * /^([A-Za-z_\\\\]+){([A-Za-z0-9_]+):(Transient|Singleton)}$/D
     * </code>
     * 
     * The first subexpression is the class name, the second
     * subexpression is the configuration name, and the third
     * subexpression is the lifecycle setting.
     *
     * A transient lifecycle setting means everytime the object is
     * requested, it will be created by the DIC while singleton
     * lifecycle setting means the DIC will only create it once and
     * caches it.
     *
     * @param string $instanceName The instance name string.
     *
     */
    public function __construct($instanceName)
    {
        $matches = array();
        $instanceName = ltrim($instanceName, '\\');
        $regexMatch = preg_match('/^([A-Za-z_\\\\]+){([A-Za-z0-9_]+):(Transient|Singleton)}$/D', $instanceName, $matches);
        
        if (!$regexMatch or count($matches) != 4 or
            empty($matches[1]) or empty($matches[2]) or
            empty($matches[3]))
        {
            throw new InvalidArgumentException("Object reference instantiation error. '{$instanceName}' is not a valid instance name.");
        }
        
        $this->instanceName = $instanceName;
        $this->className = $matches[1];
        $this->configurationName = $matches[2];
        $this->lifecycleSetting = $matches[3];
    }
    
    /**
     * Returns the instance name without backslash prefix.
     *
     * @param string Instance name without backslash prefix.
     *
     */
    public function getInstanceName()
    {
        return $this->instanceName;
    }
    
    /**
     * Returns the class name without backslash prefix.
     *
     * @return string The class name without backslash prefix.
     *
     */
    public function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Returns the lifecycle setting.
     *
     * The lifecycle setting string is either 'Singleton' or
     * 'Transient'. There is no other option.
     *
     * @return string Lifecycle setting, either 'Singleton' or 'Transient'.
     *
     */
    public function getLifecycleSetting()
    {
        return $this->lifecycleSetting;
    }
    
    /**
     * Returns the configuration name string.
     *
     * @return string The configuration name.
     *
     */
    public function getConfigurationName()
    {
        return $this->configurationName;
    }
}