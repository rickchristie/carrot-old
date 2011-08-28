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
 * Dependency Injector Configuration
 * 
// ---------------------------------------------------------------
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection;

class BindingsConfig implements ConfigInterface
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
     * @var array List of default instance names.
     */
    protected $defaults;
    
    /**
     * @var array Contains provider callback bindings.
     */
    protected $providerCallbackBindings;
    
    /**
     * @var array Contains the cache of ready-to-be-returned configurations.
     */
    protected $cache = array();
    
    /**
     * @var string Absolute path to the cache file.
     */
    protected $cacheFilePath;
    
    /**
     * @var bool TRUE if the cache has been changed.
     */
    protected $cacheChanged = FALSE;
    
    /**
     * @var array Contains allowed lifecyle setting strings.
     */
    protected $allowedLifecycles = array('Singleton', 'Transient');
    
    /**
     * Activate the usage of cache file.
     * 
    // ---------------------------------------------------------------
     * A lot of configuration can be defined implicitly by setting
     * default instance name to use when 
     * 
     * @param string $cacheFilePath Absolute path to the cache file.
     * 
     */
    public function useCache($cacheFilePath)
    {
        $this->cacheFilePath = $cacheFilePath;
        
        if (file_exists($cacheFilePath))
        {
            $cacheString = file_get_contents($cacheFilePath);
            $unserializedCache = unserialize($cacheString);
            
            if (is_array($unserializedCache))
            {
                $this->cache = $unserializedCache;
            }
        }
    }
    
    /**
     * Get configuration for the instance.
     * 
    // ---------------------------------------------------------------
     * May return a constructor arguments array, provider class array,
     * or provider callback array.
     *
     * Example constructor arguments configuration array:
     *
     * <code>
     * $config = array(
     *     'type' => Configuration::CONSTRUCTOR_ARGS,
     *     'args' => array(
     *         'A string argument',
     *         $reference
     *     )
     * );
     * </code>
     * 
     * Example of provider class configuration array:
     *
     * <code>
     * $config = array(
     *     'type' => Configuration::PROVIDER_CLASS,
     *     'provider' => $providerReference
     * );
     * </code>
     *
     * Example of provider callback configuration array:
     *
     * <code>
     * $config = array(
     *     'type' => Configuration::PROVIDER_CALLBACK,
     *     'provicer' => $callback,
     *     'args' => array(
     *         'A string argument',
     *         $reference
     *     )
     * );
     * </code>
     * 
     * @param string $className 
     * @param string $configName 
     * @param string $lifecycleSetting 
     * 
     */
    public function getConfig($className, $configName, $lifecycle)
    {
        
    }
    
    /**
     * Set default instance name to use.
     * 
     * 
     * 
     * @param string $className 
     * @param string $configName 
     * @param string $lifecycleSetting 
     * 
     */
    public function setDefaultInstanceName($className, $configName, $lifecycle)
    {
        if (!in_array($lifecycle, $this->allowedLifecycles))
        {
            throw new InvalidArgumentException("DIConfig error in setting default instance. Unknown lifecycle setting '{$lifecycle}'. Lifecycle setting must be either 'Transient' or 'Singleton'.");
        }
        
        $className = ltrim($className, '\\');
        $this->defaults[$className]['default'] = array(
            'configName' => $configName,
            'lifecycle' => $lifecycle
        );
    }
    
    /**
     * Set default instance to use when encountering 
     * 
     * 
     * 
     */
    public function setDefaultInstanceNameForNamespace($className, $configName, $lifecycle, $namespace)
    {
        if (!in_array($lifecycle, $this->allowedLifecycles))
        {
            throw new InvalidArgumentException("DIConfig error in setting default instance for namespace. Unknown lifecycle setting '{$lifecycle}'. Lifecycle setting must be either 'Transient' or 'Singleton'.");
        }
        
        $className = ltrim($className, '\\');
        $this->defaults[$className]['namespace'][$namespace] = array(
            'configName' => $configName,
            'lifecycle' => $lifecycle
        );
    }
    
    /**
     * Set default reference 
     * 
     * 
     * 
     */
    public function setDefaultInstanceNameForClass($className, $configName, $lifecycle, $specificClass)
    {
        if (!in_array($lifecycle, $this->allowedLifecycles))
        {
            throw new InvalidArgumentException("DIConfig error in setting default instance for specific class. Unknown lifecycle setting '{$lifecycle}'. Lifecycle setting must be either 'Transient' or 'Singleton'.");
        }
        
        $className = ltrim($className, '\\');
        $this->defaults[$className]['class'][$specificClass] = array(
            'configName' => $configName,
            'lifecycle' => $lifecycle
        );
    }
    
    /**
     * Bind a provider class.
     * 
     * 
    // ---------------------------------------------------------------
     * Provider and constructor argument bindings are automatically
     * inserted into the cache array. This is because they do not need
     * to be processed anymore before returned to the server.
     * 
     * @param string $className
     * @param string $configName
     * @param string $lifecycle
     * 
     */
    public function bindProvider($className, $configName, $lifecycle, $providerClassName, $providerConfigName, $providerLifecycle)
    {
        $className = ltrim($className, '\\');
        $this->cache
    }
    
    /**
     * Bind a provider callback.
     * 
     * @param string
     * @param string
     * @param string 
     * 
     */
    public function bindProviderCallback($className, $configName, $lifecycle, $callback, $args = NULL)
    {
        $this->
    }
    
    /**
     * Bind constructor arguments.
     * 
     * 
     * 
     */
    public function bindConstructorArgs($className, $configName, $lifecycle, array $args)
    {
        
    }
    
    public function __destruct()
    {
        if ($this->cacheChanged)
        {
            $this->saveCache();
        }
    }
    
    protected function saveCache()
    {
        
    }
}