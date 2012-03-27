<?php

namespace Carrot\Autopilot;

/**
 * Refers to a specific instance that is instantiated with
 * Autopilot's container.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Reference
{
    /**
     * Fully qualified class name of the referenced instance.
     *
     * @var string $className
     */
    private $className;
    
    /**
     * Configuration name of the referenced instance.
     *
     * @var string $configurationName
     */
    private $configurationName;
    
    /**
     * Lifecycle setting of the referenced instance.
     *
     * @var string $lifecycle
     */
    private $lifecycle;
    
    /**
     * The regular expression pattern used to validate the reference
     * ID and break it into parts.
     * 
     * @var string $regex
     */
    private $regex = '/^([A-Za-z\\\\_0-9]+)(@([A-Za-z_0-9]+))?(:(Singleton|Transient))?$/';
    
    /**
     * Constructor.
     * 
     * Reference IDs refer to a specific dependency injection
     * configuration. A full reference ID consists of full class
     * name, configuration name, and lifecycle setting:
     * 
     * <pre>
     * Carrot\MySQLi\MySQLi@Main:Singleton
     * </pre>
     * 
     * The lifecycle setting can be either Transient or Singleton,
     * is optional and defaults to Singleton, so the above reference
     * name can be simplified as such:
     * 
     * <pre>
     * Carrot\MySQLi\MySQLi@Main
     * </pre>
     * 
     * The configuration name is specific to each class, and is also
     * optional. If you don't have more than one injection
     * configuration for the class, you might want to consider
     * leaving the configuration name out, creating an 'unnamed
     * reference' (a fancy name for reference with empty
     * configuration name string):
     * 
     * <pre>
     * Carrot\MySQLi\MySQLi
     * </pre>
     * 
     * The above reference name essentially is the same as:
     * 
     * <pre>
     * Carrot\MySQLi\MySQLi@:Singleton
     * </pre>
     * 
     * Unnamed reference is used for defaults when the configuration
     * object tries to automatically resolve dependencies.
     * 
     * @param string $referenceId
     *
     */
    public function __construct($referenceId)
    {
        $result = preg_match_all($this->regex, $referenceId, $matches);
        
        if ($result <= 0)
        {
            throw new RuntimeException('Reference ID is not valid.');
        }
        
        $this->className = trim($matches[0][0], '\\');
        $this->configurationName = $matches[3][0];
        $this->lifecycle = 'Singleton';
        
        if (!empty($matches[5][0]))
        {
            $this->lifecycle = $matches[5][0];
        }
    }
    
    /**
     * Get the fully qualified class name of the referenced instance,
     * without backslash prefix and suffix.
     * 
     * @return string
     *
     */
    public function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Get the configuration name of the referenced instance.
     * 
     * @return string
     *
     */
    public function getConfigurationName()
    {
        return $this->configurationName;
    }
    
    /**
     * Check if the referenced object is singleton or not.
     * 
     * @return bool
     *
     */
    public function isSingleton()
    {
        return ($this->lifecycle == 'Singleton');
    }
    
    /**
     * Get the complete reference ID string.
     * 
     * @return string
     *
     */
    public function getId()
    {
        return "{$this->className}@{$this->configurationName}:{$this->lifecycle}";
    }
}