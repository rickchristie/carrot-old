<?php

namespace Carrot\Autopilot;

use Carrot\Autopilot\Rulebook\ReflectionRulebook,
    Carrot\Autopilot\Rulebook\SetterRulebook,
    Carrot\Autopilot\Rulebook\StandardRulebook,
    Carrot\Autopilot\Rulebook\SubstituteRulebook;

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
     * @var Cache $cache
     */
    private $cache;
    
    /**
     * Used to store and consult rules regarding object substitution.
     * 
     * @var SubstituteRulebook $substituteRulebook
     */
    private $substituteRulebook;
    
    /**
     * Used to store and consult standard rules regarding ctor,
     * callback, and provider instantiators.
     * 
     * @var StandardRulebook $standardRulebook
     */
    private $standardRulebook;
    
    /**
     * Used to store and consult rules for automatic instantiator
     * generation via reflection.
     * 
     * @var ReflectionRulebook $reflectionRulebook
     */
    private $reflectionRulebook;
    
    /**
     * Used to store and consult rules regarding setters.
     * 
     * @var SetterRulebook $setterRulebook
     */
    private $setterRulebook;
    
    /**
     * Constructor.
     * 
     * @param Cache $cache
     * @param StandardRulebook $standardRulebook
     * @param ReflectionRulebook $reflectionRulebook
     * @param SubstituteRulebook $substituteRulebook
     * @param SetterRulebook $setterRulebook
     *
     */
    public function __construct(
        Cache $cache,
        StandardRulebook $standardRulebook,
        ReflectionRulebook $reflectionRulebook,
        SubstituteRulebook $substituteRulebook,
        SetterRulebook $setterRulebook
    )
    {
        $this->cache = $cache;
        $this->standardRulebook = $standardRulebook;
        $this->reflectionRulebook = $reflectionRulebook;
        $this->substituteRulebook = $substituteRulebook;
        $this->setterRulebook = $setterRulebook;
    }
    
    /**
     * Sets a default value to use when generating instantiator via
     * ReflectionRulebook.
     * 
     * @see ReflectionRulebook
     * @param string $contextString The context to use the default value.
     * @param string $varName The constructor argument name.
     * @param mixed $value The value to use.
     *
     */
    public function set($contextString, $varName, $value)
    {
        $this->reflectionRulebook->setDefaultValue(
            $contextString,
            $varName,
            $value
        );
    }
    
    /**
     * Similar to set(), but used for setting many default values
     * in one call.
     * 
     * @see ReflectionRulebook
     * @param string $contextString The context to use the default values.
     * @param string $varName The constructor argument name.
     *
     */
    public function setMany($contextString, array $args)
    {
        foreach ($args as $varName => $value)
        {
            $this->reflectionRulebook->setDefaultValue(
                $contextString,
                $varName,
                $value
            );
        }
    }
    
    public function sub()
    {
        
    }
    
    public function useCtor()
    {
        
    }
    
    public function useCallback()
    {
        
    }
    
    public function useProvider()
    {
        
    }
    
    public function setAfter()
    {
        
    }
    
    public function setInstantiator()
    {
        
    }
    
    /**
    //---------------------------------------------------------------
     * 
     *
     */
    public function getInstantiator(
        Reference $reference,
        Reference $contextReference
    )
    {
        
    }
    
    /**
    //---------------------------------------------------------------
     * 
     *
     */
    public function getSetter(
        Reference $reference,
        Reference $contextReference
    )
    {
        
    }
}