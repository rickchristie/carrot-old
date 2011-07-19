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
 * {Fully qualified class name}@{Configuration name}:{Singleton|Transient}
 * Carrot\Core\FrontController@Main:Transient
 * Carrot\Database\MySQLi@Backup:Singleton
 * </code>
 *
 * The configuration name and the lifecycle setting will form the
 * method name to be called on the provider. This means for the
 * two examples above, the method called will be:
 *
 * <code>
 * FrontControllerProvider::getMainSingleton()
 * MySQLiProvider::getBackupSingleton()
 * </code>
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

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
     * /^([A-Za-z_\\\\]+)@([A-Za-z0-9_]+):(transient|singleton)$/D
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
        $regexMatch = preg_match('/^([A-Za-z_\\\\]+)@([A-Za-z0-9_]+):(Transient|Singleton)$/D', $instanceName, $matches);
        
        if (!$regexMatch or count($matches) != 4 or
            empty($matches[1]) or empty($matches[2]) or
            empty($matches[3]))
        {
            throw new InvalidArgumentException("InstanceName instantiation error. '{$instanceName}' is not a valid instance name.");
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
     * Gets the method name to be called on the provider class.
     *
     * The method name is the configuration name and the lifecycle
     * setting concatenated with the prefix 'get'. For example, with
     * the configuration name 'Main' and the lifecycle setting
     * 'Transient', the method name would be:
     *
     * <code>
     * getMainTransient()
     * </code>
     *
     * @return string The method name to be called on the provider class.
     *
     */
    public function getProviderMethodName()
    {
        return 'get' . $this->configurationName . $this->lifecycleSetting;
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