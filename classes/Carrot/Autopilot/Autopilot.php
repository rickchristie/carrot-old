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
     * @see useCtor()
     * @var string CTOR
     */
    const CTOR = 'c';
    
    /**
     * @see useProvider()
     * @var string PROVIDER
     */
    const PROVIDER = 'p';
    
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
     * @see get()
     * @var Stack $stack
     */
    private $stack;
    
    /**
     * @see __construct()
     * @var ObjectCache $objectCache
     */
    private $objectCache;
    
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        $wildcardContext = new Context('*');
        $this->currentContext = $wildcardContext;
        $this->contextCache['*'] = $wildcardContext;
        $this->objectCache = new ObjectCache;
        $this->stack = new Stack($objectCache);
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
            $item = $this->stack->getLast();
            
            if ($item->isInstantiated() == FALSE)
            {
                if ($item->has
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
     * Prepare the instantiator, either by automatic wiring or manual
     * overrides.
     * 
     * @param string $identifierString
     * @return InstantiatorInterface
     *
     */
    private function prepareInstantiator()
    {
        
    }
    
    /**
     * Prepare the setter injector.
     * 
     * @param string $identifierString
     * @return SetterInterface
     *
     */
    private function prepareSetters()
    {
        
    }
}