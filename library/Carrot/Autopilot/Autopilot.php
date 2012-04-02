<?php

namespace Carrot\Autopilot;

use InvalidArgumentException,
    RuntimeException,
    Carrot\Autopilot\Exception\CannotFindInstantiatorException,
    Carrot\Autopilot\Instantiator\InstantiatorInterface,
    Carrot\Autopilot\Instantiator\Rulebook\InstantiatorRulebookInterface,
    Carrot\Autopilot\Instantiator\Rulebook\ReflectionRulebook,
    Carrot\Autopilot\Instantiator\Rulebook\StandardRulebook,
    Carrot\Autopilot\Instantiator\Rulebook\SubstitutionRulebook,
    Carrot\Autopilot\Setter\SetterInterface,
    Carrot\Autopilot\Setter\Rulebook\SetterRulebookInterface,
    Carrot\Autopilot\Setter\Rulebook\SetterStandardRulebook;

/**
 * Used by the Container to get instantiators and setters.
 * 
 * Acts as a helper for manipulating the rulebooks, also uses
 * the rulebooks to create instantiators and setters on the fly.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Autopilot
{
    /**
     * Used to store already resolved instantiators and setters. Each
     * time getInstantiator() or getSetter() has a result, it is
     * saved in this runtime cache so that we don't have to consult
     * the rulebook again next time.
     * 
     * @var CacheInterface $cache
     */
    private $cache;
    
    /**
     * Used for logging the process.
     * 
     * @var AutopilotLog $log
     */
    private $log;
    
    /**
     * Contains the list of instantiator rulebooks to consult on.
     *
     * @var array $instantiatorRulebooks
     */
    private $instantiatorRulebooks = array();
    
    /**
     * Contains the list of setter rulebooks to consult on.
     * 
     * @var array $setterRulebooks
     */
    private $setterRulebooks = array();
    
    /**
     * Users can only use shortcut methods if they use default
     * rulebooks. If rulebooks have been tampered with, then
     * shortcut methods will cease to function.
     *
     * @var bool $canUseShortcutMethods
     */
    private $canUseShortcutMethods;
    
    /**
     * 
     * 
     * @var bool $logExtraInformation
     */
    private $logExtraInformation = TRUE;
    
    /**
     * The context string to be used for shortcut methods.
     * 
     * @see on()
     * @var string $currentContext
     */
    private $currentContext;
    
    /**
     * Constructor.
     * 
     * If $defaultMode is set to TRUE, then Autopilot will
     * instantiate and register a default pack of rulebooks.
     * 
     * @param CacheInterface $cache
     * @param bool $defaultMode
     *
     */
    public function __construct(CacheInterface $cache, $useDefaultRulebooks = TRUE)
    {
        $this->cache = $cache;
        
        if ($useDefaultRulebooks)
        {
            $this->resetToDefaultRulebooks();
        }
    }
    
    /**
     * Use default instantiator and setter rulebooks.
     * 
     * This method will clear any rulebooks previously set. The
     * default instantiator rulebooks are as follows (highest
     * priority first):
     * 
     * - Carrot\Autopilot\Instantiator\Rulebook\SubstitutionRulebook
     * - Carrot\Autopilot\Instantiator\Rulebook\StandardRulebook
     * - Carrot\Autopilot\Instantiator\Rulebook\ReflectionRulebook
     * 
     * You can access them using the index name 'substitution',
     * 'standard', and 'reflection'.
     * 
     * Only one setter rulebook is used:
     * 
     * - Carrot\Autopilot\Setter\Rulebook\StandardRulebook
     * 
     * You can access it by using the index name 'standard'.
     * 
     * Resetting the rulebooks will allow the usage of shortcut
     * methods.
     *
     */
    public function resetToDefaultRulebooks()
    {
        $this->instantiatorRulebooks = array(
            'substitution' => new SubstitutionRulebook,
            'standard' => new StandardRulebook,
            'reflection' => new ReflectionRulebook
        );
        
        $this->setterRulebooks = array(
            'standard' => new SetterStandardRulebook
        );
        
        $this->canUseShortcutMethods = TRUE;
    }
    
    /**
     * Add an instantiator rulebook to the list.
     * 
     * Rulebook consultation has FIFO policy, first rulebook added
     * is the first to be consulted.
     * 
     * Shortcut methods will cease to work if this method is called.
     * 
     * @param string $index
     * @param InstantiatorRulebookInterface $rulebook
     *
     */
    public function addInstantiatorRulebook(
        $index,
        InstantiatorRulebookInterface $rulebook
    )
    {
        $this->instantiatorRulebooks[$index] = $rulebook;
        $this->canUseShortcutMethods = FALSE;
    }
    
    /**
     * Add a setter rulebook to the list.
     * 
     * Rulebook consultation has FIFO policy, first rulebook added
     * is the first to be consulted.
     * 
     * Shortcut methods will cease to work if this method is called.
     * 
     * @param string $index
     * @param SetterRulebookInterface $rulebook
     *
     */
    public function addSetterRulebook(
        $index,
        SetterRulebookInterface $rulebook
    )
    {
        $this->setterRulebooks[$index] = $rulebook;
        $this->canUseShortcutMethods = FALSE;
    }
    
    /**
     * Get instantiator rulebook with the given index name.
     * 
     * @throws InvalidArgumentException If the rulebook doesn't exist.
     * @param string $index
     * @return InstantiatorRulebookInterface
     *
     */
    public function getInstantiatorRulebook($index)
    {
        if (array_key_exists($index, $this->instantiatorRulebooks))
        {
            throw new InvalidArgumentException("Cannot find instantiator rulebook registered with the index name '{$index}'.");
        }
        
        return $this->instantiatorRulebooks[$index];
    }
    
    /**
     * Get setter rulebook with the given index name.
     * 
     * @throws InvalidArgumentException If the rulebook doesn't exist.
     * @param string $index
     * @return SetterRulebookInterface
     *
     */
    public function getSetterRulebook($index)
    {
        if (array_key_exists($index, $this->setterRulebooks))
        {
            throw new InvalidArgumentException("Cannot find setter rulebook registered with the index name '{$index}'.");
        }
        
        return $this->setterRulebooks[$index];
    }
    
    /**
     * Consults the cache and instantiator rulebooks (FIFO) to get
     * the instantiator for the given autopilot reference.
     * 
     * This method first checks the cache before digging in to
     * rulebooks.
     * 
     * @param Reference $reference
     * @return InstantiatorInterface
     *
     */
    public function getInstantiator(Reference $reference)
    {
        $result = $this->cache->getInstantiator($reference, $this->log);
        
        if ($result instanceof InstantiatorInterface)
        {
            $this->log->logUsingInstantiator($result);
            return $result;
        }
        
        $result = $this->getInstantiatorFromRulebooks($reference);
        
        if ($result instanceof InstantiatorInterface == FALSE)
        {
            $id = $reference->getId();
            throw new CannotFindInstantiatorException("Cannot find any matching rules to create instantiator for '{$id}'");
        }
        
        $this->log->logUsingInstantiator($result);
        return $result;
    }
    
    /**
     * Consults the cache and instantiator rulebooks (FIFO) to get
     * the setter for the given autopilot reference.
     * 
     * This method first checks the cache before digging in to
     * rulebooks. If there is no setter then this method will return
     * FALSE.
     * 
     * @param Reference $reference
     * @return SetterInterface|FALSE
     *
     */
    public function getSetter(Reference $reference)
    {
        $result = $this->cache->getSetter($reference, $this->log);
        
        if ($result instanceof SetterInterface)
        {
            $this->log->logUsingSetter($result);
            return $result;
        }
        
        $result = $this->getSetterFromRulebooks($reference);
        
        if ($result instanceof SetterInterface)
        {
            $this->log->logUsingSetter($result);
            return $result;
        }
        
        return FALSE;
    }
    
    /**
     * Get the logger instance.
     * 
     * @return AutopilotLog
     *
     */
    public function getLog()
    {
        return $this->log;
    }
    
    /**
     * Get the cache instance.
     * 
     * @return CacheInterface
     *
     */
    public function getCache()
    {
        return $this->cache;
    }
    
    /**
     * Sets the context for calling the shortcut methods.
     * 
     * @param string $contextString
     * @return Autopilot Returns itself to allow chaining.
     *
     */
    public function on($contextString)
    {
        $this->currentContext = $contextString;
        return $this;
    }
    
    /**
     * Sets default constructor arguments for the reflection rulebook
     * for the current context.
     * 
     * This is a shortcut method and can only be run with default
     * rulebook settings.
     * 
     * Use it to define default constructor arguments for the context
     * set with on():
     * 
     * <pre>
     * $autopilot->on('Carrot\MySQLi\MySQLi@Main')->def(
     *     'hostname',
     *     'localhost'
     * );
     * </pre>
     * 
     * @see ReflectionRulebook::setDefaultValue()
     * @param string $varName Variable name on the constructor.
     * @param mixed $value Value to use.
     * @return Autopilot Returns itself to allow chaining.
     *
     */
    public function def($varName, $value)
    {
        $this->throwExceptionIfCannotUseShortcutMethods();
        $this->instantiatorRulebooks['reflection']->setDefaultValue(
            $this->currentContext,
            $varName,
            $value
        );
        
        return $this;
    }
    
    /**
     * Sets default constructor arguments for the reflection rulebook
     * in batch for the current context.
     * 
     * This is a shortcut method and can only be run with default
     * rulebook settings.
     * 
     * Use it to define default constructor arguments in batch for
     * the context set with on():
     * 
     * <pre>
     * $autopilot->on('Carrot\MySQLi\MySQLi@Main')->defBatch(
     *     array(
     *         'hostname' => 'localhost',
     *         'username' => 'carrot',
     *         'password' => 'awesome',
     *         'database' => 'carrot_db'
     *     )
     * );
     * </pre>
     * 
     * @see ReflectionRulebook::setDefaultValue()
     * @param array $ctorArgs List of constructor parameter names
     *        and their values.
     * @return Autopilot Returns itself to allow chaining.
     *
     */
    public function defBatch(array $ctorArgs)
    {
        $this->throwExceptionIfCannotUseShortcutMethods();
        
        foreach ($ctorArgs as $varName => $value)
        {
            $this->instantiatorRulebooks['reflection']->setDefaultValue(
                $this->currentContext,
                $varName,
                $value
            );
        }
        
        return $this;
    }
    
    /**
    //---------------------------------------------------------------
     * 
     * 
     * 
     *
     */
    public function sub()
    {
        $this->throwExceptionIfCannotUseShortcutMethods();
        
        return $this;
    }
    
    public function useCtor()
    {
        $this->throwExceptionIfCannotUseShortcutMethods();
        
        return $this;
    }
    
    public function useProvider()
    {
        $this->throwExceptionIfCannotUseShortcutMethods();
        
        return $this;
    }
    
    public function useCallback()
    {
        $this->throwExceptionIfCannotUseShortcutMethods();
        
        return $this;
    }
    
    public function set()
    {
        $this->throwExceptionIfCannotUseShortcutMethods();
        
        return $this;
    }
    
    public function setBatch()
    {
        $this->throwExceptionIfCannotUseShortcutMethods();
        
        return $this;
    }
    
    /**
     * Gets the instantiator by consulting rulebooks.
     * 
     * @see getInstantiator()
     * @param Reference $reference
     * @return InstantiatorInterface|FALSE
     *
     */
    private function getInstantiatorFromRulebooks(Reference $reference)
    {
        $result = FALSE;
        
        foreach ($this->instantiatorRulebooks as $rulebook)
        {
            $result = $rulebook->resolve($reference, $this->log);
            
            if ($result instanceof InstantiatorInterface)
            {
                break;
            }
        }
        
        return $result;
    }
    
    /**
     * Gets the setter by consulting rulebooks.
     * 
     * @see getSetter()
     * @param Reference $reference
     * @return SetterInterface|FALSE
     *
     */
    private function getSetterFromRulebooks(Reference $reference)
    {
        $result = FALSE;
        
        foreach ($this->setterRulebooks as $rulebook)
        {
            $result = $rulebook->resolve($reference);
            
            if ($result instanceof SetterInterface)
            {
                break;
            }
            else
            {
                $this->log->logSetterRulebookNotFound
            }
        }
        
        return $result;
    }
    
    /**
     * Throws exception if this autopilot instance is not using
     * default rulebooks.
     * 
     * @throws RuntimeException
     *
     */
    private function throwExceptionIfCannotUseShortcutMethods()
    {
        if ($this->canUseShortcutMethods == FALSE)
        {
            throw new RuntimeException('Cannot use shortcut methods because Autopilot is not using default rulebooks.');
        }
    }
}