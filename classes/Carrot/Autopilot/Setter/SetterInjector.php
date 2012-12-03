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
            $class = $this->identifier->getClass();
            $reflectionMethod = new ReflectionMethod(
                $class,
                $method
            );
            
            $parameters = $reflectionMethod->getParameters();
            
            if (empty($parameters))
            {
                // Setter that receives no arguments? Eh?
                // Run the method anyway, then continue.
                $object->$method();
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
                else
                {
                    // The parameter is not specified on the argument
                    // list. If default value is available use it.
                    if ($param->isDefaultValueAvailable())
                    {
                        $invokeArgs[] = $param->getDefaultValue();
                        continue;
                    }
                    
                    // Otherwise, if it's optional, set it to NULL.
                    if ($param->isOptional())
                    {
                        $invokeArgs[] = NULL;
                        continue;
                    }
                    
                    // Houston, we have a problem.
                    throw new RuntimeException("Setter injection failed for class {$class}, method {$method}. There is no value for parameter '\${$paramName}'.");
                }
            }
            
            // Invoke it already!
            
            $this->callMethod(
                $object,
                $method,
                $invokeArgs,
                $reflectionMethod
            );
        }
    }
    
    /**
     * Calls the setter method.
     * 
     * NOTE: Yes yes, I know this is ugly, but unfortunately
     * reflection is slow. So bear the ugliness.
     * 
     * @param mixed $object
     * @param string $method
     * @param array $invokeArgs
     * @param ReflectionMethod $reflectionMethod
     *
     */
    private function callMethod(
        $object,
        $method,
        array $invokeArgs,
        ReflectionMethod $reflectionMethod
    )
    {
        $count = count($invokeArgs);
        
        switch ($count)
        {
            case 0:
                $object->$method();
            break;
            case 1:
                $object->$method($invokeArgs[0]);
            break;
            case 2:
                $object->$method($invokeArgs[0], $invokeArgs[1]);
            break;
            case 3:
                $object->$method(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2]
                );
            break;
            case 4:
                $object->$method(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3]
                );
            break;
            case 5:
                $object->$method(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3],
                    $invokeArgs[4]
                );
            break;
            case 6:
                $object->$method(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3],
                    $invokeArgs[4],
                    $invokeArgs[5]
                );
            break;
            case 7:
                $object->$method(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3],
                    $invokeArgs[4],
                    $invokeArgs[5],
                    $invokeArgs[6]
                );
            break;
            case 8:
                $object->$method(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3],
                    $invokeArgs[4],
                    $invokeArgs[5],
                    $invokeArgs[6],
                    $invokeArgs[7]
                );
            break;
            case 9:
                $object->$method(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3],
                    $invokeArgs[4],
                    $invokeArgs[5],
                    $invokeArgs[6],
                    $invokeArgs[7],
                    $invokeArgs[8]
                );
            break;
            case 10:
                $object->$method(
                    $invokeArgs[0], 
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3],
                    $invokeArgs[4],
                    $invokeArgs[5],
                    $invokeArgs[6],
                    $invokeArgs[7],
                    $invokeArgs[8],
                    $invokeArgs[9]
                );
            break;
        }
        
        $reflectionMethod->invokeArgs($object, $invokeArgs);
    }
}