<?php

namespace Carrot\Autopilot;

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
    //---------------------------------------------------------------
     * @var Cache Used to store already instantiated 
     */
    private $cache;
    
    private $substituteRulebook;
    
    private $standardRulebook;
    
    private $reflectionRulebook;
    
    private $setterRulebook;
    
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        
    }
    
    public function set()
    {
        
    }
    
    
}