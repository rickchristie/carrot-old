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
     * Used to store already instantiated instantiators and setters.
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
    //---------------------------------------------------------------
     * 
     *
     */
    public function set($contextString, )
    {
        
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
}