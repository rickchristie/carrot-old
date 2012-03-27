<?php

namespace Carrot\Autopilot\Rulebook;

use Carrot\Autopilot\Context;

/**
 * Rulebook that handles the automatic generation of ctor
 * instantiator via reflection, resolves default constructor
 * arguments via contexts.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class ReflectionRulebook
{
    /**
     * List of default values, with context strings as its index.
     * 
     * @var array $defaultValues
     */
    private $defaultValues = array();
    
    /**
     * List of saved class typed context objects, with context
     * string as their indexes.
     * 
     * @var array $classContexts
     */
    private $classContexts = array();
    
    /**
     * List of saved greedy class typed context objects, with context
     * string as their indexes.
     * 
     * @var array $classGreedyContexts
     */
    private $classGreedyContexts = array();
    
    /**
     * List of saved namespace typed context objects, with context
     * string as their indexes.
     * 
     * @var array $namespaceContexts
     */
    private $namespaceContexts = array();
    
    /**
     * List of saved greedy namespace typed context objects, with
     * context string as their indexes.
     * 
     * @var array $namespaceGreedyContexts
     */
    private $namespaceGreedyContexts = array();
    
    /**
     * List of saved wildcard typed context objects, with context
     * string as their indexes.
     * 
     * @var array $wildcardContexts
     */
    private $wildcardContexts();
    
    /**
     * Sets a default constructor argument to use, if we are in the
     * provided context.
     * 
     * @param string $contextString The context to use the default value.
     * @param string $varName The constructor argument name.
     * @param mixed $value The value to use.
     *
     */
    public function setDefaultValue($contextString, $varName, $value)
    {
        $this->addContext($contextString);
        
        $this->defaultValues[$contextString][$varName] = $value;
    }
    
    /**
    //---------------------------------------------------------------
     * Adds the context to appropriate properties.
     * 
     * The contexts are separated because there is a pre-determined
     * priority logic in determining which variable we want to use.
     * 
     * 
     *
     */
    private function addContext($contextString)
    {
        if (array_key_exists($contextString, $this->contexts) == FALSE)
        {
            $this->contexts[$contextString] = new Context($contextString);
        }
    }
}