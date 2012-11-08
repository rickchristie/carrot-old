<?php

namespace Carrot\Autopilot;

/**
 * The main class for the autopilot library.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Autopilot
{
    /**
     * @see get()
     * @see ref()
     * @var array $identifierCache
     */
    private $identifierCache = array();
    
    /**
     * @see on()
     * @var array $contextCache
     */
    private $contextCache = array();
    
    /**
     * @see on()
     * @var Context $currentContext
     */
    private $currentContext;
    
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        
    }
    
    public function get($identifierString)
    {
        
    }
    
    /**
    //---------------------------------------------------------------
     * 
     *
     */
    public function ref($identifierString)
    {
        
    }
    
    public function on($contextString)
    {
        
    }
    
    public function def($name, $value)
    {
        
    }
    
    public function defBatch(array $values)
    {
        
    }
    
    public function useCtor(array $args)
    {
        
    }
    
    public function useProvider($providerClass)
    {
        
    }
    
    public function set($methodName, array $args)
    {
        
    }
}