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
     * @see __construct()
     * @var Rulebook $rulebook
     */
    private $rules;
       
    /**
     * @see get()
     * @see ref()
     * @var Collection $identifierCache
     */
    private $identifierCache;
    
    /**
     * @see on()
     * @var Collection $contextCache
     */
    private $contextCache;
    
    /**
     * @see __construct()
     * @var Collection $objectCache
     */
    private $objectCache;
    
    /**
     * @see on()
     * @var Context $currentContext
     */
    private $currentContext;
    
    /**
     * @see get()
     * @var Stack $stack
     */
    private $stack;
    
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $wildcardContext = new Context('*');
        $this->currentContext = $wildcardContext;
        $this->objectCache = new Collection;
        $this->contextCache = new Collection;
        $this->identifierCach = new Collection;
        $this->contextCache->set('*', $wildcardContext);
        $this->rulebook = new Rulebook;
        $this->stack = new Stack(
            $rulebook,
            $objectCache
        );
    }
    
    /**
     * Have the Autopilot automatically create the dependency graph
     * and instantiate the object in question, with regards of the
     * rules that has already been set.
     * 
     * @param string $identifierString
     * @return mixed
     *
     */
    public function get($identifierString)
    {
        $this->stack->clear();
        $identifier = $this->ref($identifierString);
        $this->stack->push($identifier);
        
        while ($this->stack->isNotEmpty())
        {
            $item = $this->stack->pop();
            
            // Instantiation Routine.
            if ($item->isInstantiated() == FALSE)
            {
                $identifier = $item->getIdentifier();
                
                if (
                    $item->isSingleton() AND
                    $this->objectCache->has($identifier)
                )
                {
                    // Get from cache and let execution.
                    $object = $this->objectCache->get($identifier);
                    $item->setInstance($object);
                }
                else if ($item->isReadyForInstantiation())
                {
                    $list = $item->get
                }
                else
                {
                    // Get the dependency list of the instantiator
                    // and 
                    $list = $item->getInstantiatorDependen
                }
                
                // We still need to do the setter.
                $this->stack->push($item);
            }
            
        }
    }
    
    /**
     * Instantiates a context or get one from the cache.
     * 
     * @param string $identifierString
     * @return Context
     *
     */
    public function ref($identifierString)
    {
        
        
        if (array_key_exists($identifierString, $this->identifierCache))
        {
            return $this->identifierCache[$identifierString];
        }
        
        $identifier = new Identifier($identifierString);
        $this->identifierCache[$identifierString] = $identifier;
        return $identifier;
    }
    
    /**
     * Set the current context to the given context string. This
     * method should be called first before calling def(),
     * defBatch(), and set() methods.
     * 
     * @param string $contextString
     *
     */
    public function on($contextString)
    {
        if (array_key_exists($contextString, $this->contextCache))
        {
            $this->currentContext = $this->contextCache[$contextString];
        }
        
        $context = new Context($contextString);
        $this->currentContext = $context;
        $this->contextCache[$contextString] = $context;
    }
    
    /**
     * Define a default variable to be used in automatic wiring in
     * the case of non-object constructor arguments.
     * 
     * @see on()
     * @param string $name
     * @param string $value
     *
     */
    public function def($name, $value)
    {
        $contextString = $this->currentContext->get();
        $this->autoVars[$contextString][$name] = $value;
    }
    
    /**
     * Define a batch of default variables to be used in automatic
     * wiring in the case of non-object constructor arguments.
     * 
     * @see on()
     * @param array $values
     *
     */
    public function defBatch(array $values)
    {
        $contextString = $this->currentContext->get();
        
        foreach ($values as $key => $value)
        {
            $this->autoVars[$contextString][$key] = $value;
        }
    }
    
    /**
     * Run a setter for the current context with the given argument.
     * 
     * @see on()
     * @param string $methodName
     * @param array $args
     *
     */
    public function set($methodName, array $args)
    {
        $contextString = $this->currentContext->get();
        $this->setters[$contextString][] = array(
            'method' => $methodName,
            'args' => $args
        );
    }
    
    /**
     * Manually override automatic wiring for the given identifier
     * to run the constructor with the given arguments.
     * 
     * @param string $identifierString
     * @param array $args
     *
     */
    public function useCtor($identifierString, array $args)
    {
        $this->manualOverrides[$identifierString] = array(
            'type' => self::CTOR,
            'args' => $args
        );
    }
    
    /**
     * Manually override automatic wiring for the given identifier
     * to be instantiated with the given provider, running the
     * given method name.
     * 
     * Example usage:
     * 
     * <pre>
     * $autopilot->useProvider(
     *     'MySQLi@Default',
     *     'Acme\Factory\MySQLiFactory',
     *     'instantiateByConfig',
     *     array(
     *         $autopilot->ref('Acme\Config'),
     *         ...
     *     )
     * );
     * </pre>
     * 
     * @param string $identifierString
     * @param string $providerIdentifier
     * @param string $methodName
     * @param array $args
     *
     */
    public function useProvider(
        $identifierString,
        $providerIdentifier,
        $methodName,
        array $args = array()
    )
    {
        $this->manualOverrides[$identifierString] = array(
            'type' => self::PROVIDER,
            'provider' => $providerIdentifier,
            'method' => $methodName,
            'args' => $args
        );
    }
    
    /**
     * Shorthand for Rulebook::declareTransient().
     * 
     * @param string $identfierString
     *
     */
    public function declareTransient($identifierString)
    {
        $this->rules->declareTransient($identifierString);
    }
}