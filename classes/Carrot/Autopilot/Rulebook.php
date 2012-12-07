<?php

namespace Carrot\Autopilot;

/**
 * Encapsulates rules information like variables, contexts etc.
 * Also responsible for the creation of instantiators and setters
 * based on the given rules.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Rulebook
{
    /**
     * @see def()
     * @var array $autoVars
     */
    private $autoVars = array();
    
    /**
     * @see set()
     * @var array $setters
     */
    private $setters = array();
    
    /**
     * @see useCtor()
     * @see useProvider()
     * @var array $manualOverrides
     */
    private $manualOverrides = array();
    
    /**
     * @see declareTransient()
     * @var array $transients
     */
    private $transients = array();
    
    /**
     * Put variable to the rulebook to be used with the given name.
     * 
     * @param Context $context
     * @param string $name
     * @param mixed $value
     *
     */
    public function defineAutoVar(
        Context $context,
        $name,
        $value
    )
    {
        
    }
    
    /**
     * Put a rule to run setter for the given context with the given
     * method and arguments.
     * 
     * @param Context $context
     * @param string $name
     * @param mixed $value
     *
     */
    public function setSetter(
        Context $context,
        $methodName,
        array $args
    )
    {
        
    }
    
    /**
     * Manually override instantiation by running the constructor
     * with the given arguments.
     * 
     * @param Identifier $identifier
     * @param array $args
     *
     */
    public function manualCtor(
        Identifier $identifier,
        array $args
    )
    {
        
    }
    
    /**
     * Manually override instantiation by running the provider method
     * with the given arguments.
     * 
     * @param Identifier $identifier
     * @param array $args
     *
     */
    public function manualProvider(
        Identifier $identifier,
        Identifier $providerIdentifier,
        $methodName,
        array $args = array()
    )
    {
        
    }
    
    /**
     * Returns TRUE if the given identifier is transient.
     * 
     * @param Identifier $identifier
     * @return bool
     *
     */
    public function isTransient(Identifier $identifier)
    {
        $identifierString = $identifier->get();
        return array_key_exists($identifierString, $this->transients);
    }
    
    /**
     * Declare the given identifier as a transient object, which
     * means that it will be instantiated anew each time it is
     * needed.
     * 
     * @param string $identifierString
     *
     */
    public function declareTransient($identifierString)
    {
        $this->transients[$identifierString] = TRUE;
    }
    
    /**
    //---------------------------------------------------------------
     * Generate instantiator 
     *
     */
    public function generateInstantiator(Identifier $identifier)
    {
        
    }
    
    /**
    //---------------------------------------------------------------
     * Generate 
     *
     */
    public function generateSetter(Identifier $identifier)
    {
        
    }
}