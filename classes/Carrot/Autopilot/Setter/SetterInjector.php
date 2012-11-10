<?php

namespace Carrot\Autopilot\Setter;

use ReflectionMethod,
    RuntimeException,
    Carrot\Autopilot\DependencyList,
    Carrot\Autopilot\Identifier;

/**
 * Primary setter injector, not sure if planning for more, the
 * creation of the interface is just a knee-jerk reaction.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class SetterInjector implements SetterInterface
{
    /**
     * @see getDependencyList()
     * @var DependencyList $list
     */
    private $list;
    
    /**
     * @see getIdentifier()
     * @var Identifier $identifier
     */
    private $identifier;
    
    /**
     * @see addToInjectionList()
     * @var array $data
     */
    private $data = array();
    
    /**
     * Constructor.
     * 
     * @param Identifier $identifier
     * @param DependencyList $list
     *
     */
    public function __construct(
        Identifier $identifier,
        DependencyList $list
    )
    {
        $this->list = $list;
        $this->identifier = $identifier;
    }
    
    /**
     * Get the dependency list needed to run the setters.
     * 
     * @return DependencyList
     *
     */
    public function getDependencyList()
    {
        return $this->list;
    }
    
    /**
     * Get the identifier of the object to be setter-injected by
     * this class.
     * 
     * @return Identifier
     *
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    /**
     * Add the given method name and argument to the list of setter
     * injection that must be done when inject() is called.
     * 
     * Identifier instances will be inserted to the dependency list,
     * this will allow Autopilot to recursively generate the
     * dependency graph.
     * 
     * @param string $methodName
     * @param array $args
     *
     */
    public function addToInjectionList(
        $methodName,
        array $args
    )
    {
        foreach ($args as $value)
        {
            if ($value instanceof Identifier)
            {
                $this->list->add($value);
            }
        }
        
        $this->data[] = array(
            'method' => $methodName,
            'args' => $args
        );
    }
    
    /**
     * Returns TRUE if the dependencies has been fulfilled.
     * 
     * @return bool
     *
     */
    public function isReadyForInjection()
    {
        return $this->list->isFulfilled();
    }
    
    /**
     * Runs the setter injection process.
     * 
     * @param mixed $object
     *
     */
    public function inject($object)
    {
        if ($this->isReadyForInjection() == FALSE)
        {
            throw new RuntimeException('Error in setter injection: setter dependencies not yet fulfilled.');
        }
        
        foreach ($this->data as $inject)
        {
            $method = $inject['method'];
            $args = $inject['args'];
            $invokeArgs = array();
            $reflectionMethod = new ReflectionMethod(
                $this->identifier->getClass(),
                $method
            );
            
            $parameters = $reflectionMethod->getParameters();
            
            if (empty($parameters))
            {
                // Setter that receives no arguments? Eh?
                // Run the method anyway, then continue.
                $reflectionMethod->invoke($object);
                continue;
            }
            
            // Otherwise prepare the arguments.
            foreach ($parameters as $param)
            {
                $paramName = $param->getName();
                
                if (array_key_exists($paramName, $args))
                {
                    if ($args[$paramName] instanceof Identifier)
                    {
                        // Autopilot dependency parameter, get it
                        // from the dependency list.
                        $invokeArgs[] = $this->list->getObject(
                            $args[$paramName]->get()
                        );
                    }
                    else
                    {
                        // Non object or non Autopilot dependency.
                        // Just pass the argument.
                        $invokeArgs[] = $args[$paramName];
                    }
                }
            }
            
            // Invoke it already!
            $reflectionMethod->invokeArgs($object, $invokeArgs);
        }
    }
}