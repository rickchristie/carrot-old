<?php

namespace Carrot\Autopilot\Instantiator;

use ReflectionClass,
    Carrot\Autopilot\Identifier,
    Carrot\Autopilot\DependencyList;

/**
 * Generic constructor injector, both used by automatic wiring
 * and manual overrides.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class CtorInjector implements InstantiatorInterface
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
     * @see __construct()
     * @var array $args
     */
    private $args;
    
    /**
     * Constructor.
     * 
     * @param Identifier $identifier
     * @param DependencyList $list
     * @param array $args
     *
     */
    public function __construct(
        Identifier $identifier,
        DependencyList $list,
        array $args = array()
    )
    {
        $this->identifier = $identifier;
        $this->list = $list;
        $this->args = $args;
        
        foreach ($this->args as $value)
        {
            if ($value instanceof Identifier)
            {
                $this->list->add($value);
            }
        }
    }
    
    /**
     * Returns the dependency list needed to run the constructor.
     * 
     * @return DependencyList
     *
     */
    public function getDependencyList()
    {
        return $this->list;
    }
    
    /**
     * Returns the Identifier instance of the object this
     * instantiator is supposed to instantiate.
     * 
     * @return Identifier
     *
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
     * Runs the instantiation process, returns the instantiated
     * object.
     * 
     * @return mixed
     *
     */
    public function instantiate()
    {
        $class = $this->identifier->getClass();
        $reflectionClass = new ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();
        $parameters =  $constructor->getParameters();
        $invokeArgs = array();
        
        if (empty($parameters))
        {
            // Constructor has empty parameters, well
            // we know what to do :).
            return new $class;
        }
        
        foreach ($parameters as $param)
        {
            $paramName = $param->getName();
            
            if (array_key_exists($paramName, $this->args))
            {
                if ($this->args[$paramName] instanceof Identifier)
                {
                    // Autopilot dependency parameter, get it
                    // from the dependency list.
                    $invokeArgs[] = $this->list->getObject(
                        $this->args[$paramName]->get()
                    );
                }
                else 
                {
                    // Non Autopilot dependency, just put it in.
                    $invokeArgs[] = $this->args[$paramName];
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
                throw new RuntimeException("Constructor injection failed for class {$class}. There is no value for constructor parameter '\${$paramName}'.");
            }
        }
        
        return $this->callConstructor(
            $invokeArgs,
            $reflectionClass
        );
    }
    
    /**
     * Calls the constructor of the class that this instantiator is
     * supposed to instantiate.
     * 
     * NOTE: Yes yes, I know this is ugly, but unfortunately
     * reflection is slow. So bear the ugliness.
     * 
     * @param array $invokeArgs
     * @param ReflectionClass $reflectionClass
     * @return mixed
     *
     */
    private function callConstructor(
        array $invokeArgs,
        ReflectionClass $reflectionClass
    )
    {
        $count = count($invokeArgs);
        $class = $this->identifier->getClass();
        
        switch ($count)
        {
            case 0:
                return new $class;
            break;
            case 1:
                return new $class($invokeArgs[0]);
            break;
            case 2:
                return new $class($invokeArgs[0], $invokeArgs[1]);
            break;
            case 3:
                return new $class(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2]
                );
            break;
            case 4:
                return new $class(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3]
                );
            break;
            case 5:
                return new $class(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3],
                    $invokeArgs[4]
                );
            break;
            case 6:
                return new $class(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3],
                    $invokeArgs[4],
                    $invokeArgs[5]
                );
            break;
            case 7:
                return new $class(
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
                return new $class(
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
                return new $class(
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
                return new $class(
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
        
        return $reflectionClass->newInstanceArgs($invokeArgs);
    }
}