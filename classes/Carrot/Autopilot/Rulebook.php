<?php

namespace Carrot\Autopilot;

use RuntimeException,
    ReflectionClass,
    ReflectionMethod,
    Carrot\Autopilot\Instantiator\CtorInjector,
    Carrot\Autopilot\Instantiator\ProviderInjector,
    Carrot\Autopilot\Setter\SetterInjector;

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
     * @see manualCtor()
     * @var string CTOR
     */
    const CTOR = 'c';
    
    /**
     * @see manualProvider()
     * @var string PROVIDER
     */
    const PROVIDER = 'p';
    
    /**
     * @see defineAutoVar()
     * @var array $contexts
     */
    private $autoVarContexts = array();
    
    /**
     * @see setSetter()
     * @var array $setterContexts
     */
    private $setterContexts = array();
    
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
     * Put variable to the rulebook to be used with the given name
     * in automatic wiring.
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
        $contextString = $context->get();
        $this->autoVarContexts[$contextString] = $context;
        $this->autoVars[$contextString][$name] = $value;
    }
    
    /**
     * Put a rule to run setter for the given context with the given
     * method and arguments.
     * 
     * @param Context $context
     * @param string $methodName
     * @param array $args
     *
     */
    public function setSetter(
        Context $context,
        $methodName,
        array $args
    )
    {
        $contextString = $context->get();
        $this->setterContexts[$contextString] = $context;
        $this->setters[$contextString][] = array(
            'method' => $methodName,
            'args' => $args
        );
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
        $identifierString = $identifier->get();
        $this->manualOverrides[$identifierString] = array(
            'id' => $identifier,
            'type' => self::CTOR,
            'args' => $args
        );
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
        $identifierString = $identifier->get();
        $this->manualOverrides = array(
            'id' => $identifier,
            'providerId' => $providerIdentifier,
            'method' => $methodName,
            'args' => $args
        );
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
     * Generates instantiator based on the saved rules in this
     * rulebook for the given identifier.
     * 
     * @param Identifier $identifier
     * @return InstantiatorInterface
     *
     */
    public function generateInstantiator(Identifier $identifier)
    {
        $identifierString = $identifier->get();
        
        if (array_key_exists($identifierString, $this->manualOverrides))
        {
            // Generate instantiator from manual override.
            $config = $this->manualOverrides[$identifierString];
            
            if ($config['type'] == self::CTOR)
            {
                return new CtorInjector(
                    $identifier,
                    $config['args']
                );
            }
            else if ($config['type'] == self::PROVIDER)
            {
                return new ProviderInjector(
                    $identifier,
                    $config['providerId'],
                    $config['method'],
                    $config['args']
                );
            }
        }
        
        // No manual override means we have to figure
        // things out ourselves.
        
        $class = $identifier->getClass();
        $reflectionClass = new ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();
        
        if ($constructor instanceof ReflectionMethod == FALSE)
        {
            // No constructor - just instantiate then.
            return new CtorInjector($identifier);
        }
        
        $parameters = $constructor->getParameters();
        
        if (empty($parameters))
        {
            // No parameters - just instantiate then.
            return new CtorInjector($identifier);
        }
        
        $vars = $this->generateAutoVars($identifier);
        $args = array();
        
        foreach ($parameters as $param)
        {
            $paramName = $param->getName();
            
            if (array_key_exists($paramName, $vars))
            {
                // Value exist in the variable list.
                $args[$paramName] = $vars[$paramName];
                continue;
            }
            
            $class = $param->getClass();
            
            if (empty($class) == FALSE)
            {
                $paramIdentifier = new Identifier("{$class}@Default");
                $args[$paramName] = $paramIdentifier;
                continue;
            }
            
            throw new RuntimeException("Automatic wiring failed for {$identifierString}, parameter '\${$paramName}' is not defined.");
        }
        
        return new CtorInjector(
            $identifier,
            $args
        );
    }
    
    /**
     * Generates setter based on the saved rules in this rulebook
     * for the given identifier.
     * 
     * @param Identifier $identifier
     * @return SetterInterface
     *
     */
    public function generateSetter(Identifier $identifier)
    {
        $setterInjector = new SetterInjector;
        $args = array();
        
        foreach ($this->setterContexts as $contextString => $context)
        {
            if (
                array_key_exists($contextString, $this->setters) AND
                $context->includes($identifier)
            )
            {
                foreach ($this->setters[$contextString] as $config)
                {
                    $setterInjector->addToInjectionList(
                        $config['method'],
                        $config['args']
                    );
                }
            }
        }
        
        return $setterInjector;
    }
    
    /**
     * Generate default variables from the list of contexts that
     * includes the given identifier.
     * 
     * @param Identifier $identifier
     * @return array
     *
     */
    private function generateAutoVars(Identifier $identifier)
    {
        $args = array();
        
        foreach ($this->autoVarContexts as $contextString => $context)
        {   
            if (
                array_key_exists($contextString, $this->autoVars) AND
                $context->includes($identifier)
            )
            {
                $args = array_merge($args, $this->autoVars[$contextString]);
            }
        }
        
        return $args;
    }
}