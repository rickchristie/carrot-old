<?php

namespace Carrot\Autopilot;

use Carrot\Autopilot\Setter\SetterInterface,
    Carrot\Autopilot\Instantiator\InstantiatorInterface;

/**
 * Acts as a cache for resolved instantiators and setters.
 * 
 * Cache contents can be serialized into an array that can be
 * re-loaded at runtime to improve Autopilot performance.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Cache
{
    /**
     * 
     * 
     * @var array $data
     */
    private $data = array();
    
    private $serialized = array();
    
    public function putInstantiator($referenceId, InstantiatorInterface $instantiator)
    {
        
    }
    
    public function putSetter($referenceId, SetterInterface $setter)
    {
        
    }
    
    public function getInstantiator($referenceId)
    {
        
    }
    
    public function getSetter($referenceId)
    {
        
    }
    
    public function loadSerialized(array $serialized)
    {
        
    }
    
    public function serialize()
    {
        
    }
}