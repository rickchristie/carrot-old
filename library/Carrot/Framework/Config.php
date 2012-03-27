<?php

namespace Carrot\Framework;

use InvalidArgumentException,
    Carrot\Injection\Reference;

/**
 * Represents the application's configuration.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Config
{
    /**
     * @var string $injectionsFile
     */
    private $injectionsFile;
    
    /**
     * @var string $routesFile
     */
    private $routesFile;
    
    /**
     * @var string $eventsFile
     */
    private $eventsFile;
    
    /**
     * @var Reference $exceptionHandlerReference
     */
    private $exceptionHandlerReference;
    
    /**
     * Sets the path to the dependency injection configuration file.
     * 
     * @param string $filePath Absolute path to the file.
     *
     */
    public function setInjectionsFile($filePath)
    {
        if (file_exists($filePath) == FALSE)
        {
            throw new InvalidArgumentException("The injection configuration file '{$filePath}' does not exist.");
        }
        
        $this->injectionsFile = $filePath;
    }
    
    /**
     * Get the path to the dependency injection configuration file.
     * 
     * @return string
     *
     */
    public function getInjectionsFile()
    {
        return $this->injectionsFile;
    }
    
    /**
     * Sets the path to the route configuration file.
     * 
     * @param string $filePath Absolute path to the file.
     *
     */
    public function setRoutesFile($filePath)
    {
        if (file_exists($filePath) == FALSE)
        {
            throw new InvalidArgumentException("The route configuration file '{$filePath}' does not exist.");
        }
        
        $this->routesFile = $filePath;
    }
    
    /**
     * Get the path to the route configuration file.
     * 
     * @return string
     *
     */
    public function getRoutesFile()
    {
        return $this->injectionsFile;
    }
    
    /**
     * Sets the path to the event configuration file.
     * 
     * @param string $filePath Absolute path to the file.
     *
     */
    public function setEventsFile($filePath)
    {
        if (file_exists($filePath) == FALSE)
        {
            throw new InvalidArgumentException("The event configuration file '{$filePath}' does not exist.");
        }
        
        $this->eventsFile = $filePath;
    }
    
    /**
     * Get the path to the event configuration file.
     * 
     * @return string
     *
     */
    public function getEventsFile()
    {
        return $this->injectionsFile;
    }
    
    /**
     * Checks whether or not we have valid configurations to run
     * the framework.
     * 
     * @return bool
     *
     */
    public function isFullyConfigured()
    {
        return (
            !empty($this->injectionsFile) &&
            !empty($this->routesFile) &&
            !empty($this->eventsFile)
        );
    }
    
    /**
     * Sets the reference to the class that will be used as the
     * exception handler.
     * 
     * @param Reference $reference
     *
     */
    public function setExceptionHandler(Reference $reference)
    {
        $this->exceptionHandlerReference = $reference;
    }
    
    /**
     * Gets the reference to the exception handler class.
     * 
     * @return Reference
     *
     */
    public function getExceptionHandlerReference()
    {
        if ($this->exceptionHandlerReference instanceof Reference)
        {
            return $this->exceptionHandlerReference;
        }
        
        return new Reference('Carrot\Framework\Error\ExceptionHandler');
    }
    
    /**
     * Activates debug mode or not.
     * 
     * If debug mode is active, Carrot will wrap the route destination
     * object's instantiation and method call in a try/catch block.
     * Any exception thrown will be processed into useful debug page
     * along with extra log information.
     * 
     * @param bool $isDebugModeActive
     *
     */
    public function activateDebugMode($isDebugModeActive)
    {
        $this->isDebugModeActive = (bool) $isDebugModeActive;
    }
}