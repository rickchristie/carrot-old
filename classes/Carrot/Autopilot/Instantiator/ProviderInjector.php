<?php

namespace Carrot\Autopilot\Instantiator;

use RuntimeException,
    ReflectionMethod,
    ReflectionException,
    Carrot\Autopilot\Identifier,
    Carrot\Autopilot\DependencyList;

/**
 * Generic provider injector, used for provider based manual
 * override.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class ProviderInjector implements InstantiatorInterface
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
     * @see getProviderIdentifier()
     * @var Identifier $providerIdentifier
     */
    private $providerIdentifier;
    
    /**
     * @see __construct()
     * @var string $method
     */
    private $method;
    
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
     * @param ProviderIdentifier $providerIdentifier
     * @param string $method
     * @param array $args
     *
     */
    public function __construct(
        Identifier $identifier,
        DependencyList $list,
        Identifier $providerIdentifier,
        $method,
        array $args = array()
    )
    {
        $this->identifier = $identifier;
        $this->list = $list;
        $this->providerIdentifier = $providerIdentifier;
        $this->method = $method;
        $this->args = $args;
        $this->list->add($this->providerIdentifier);
        
        foreach ($this->args as $value)
        {
            if ($value instanceof Identifier)
            {
                $this->list->add($value);
            }
        }
    }
    
    /**
     * Returns the dependency list needed to instantiate the provider
     * and run the provider method.
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
     * Runs the instantiation process. In this case, instantiates
     * the provider and runs the method with the given arguments.
     * 
     * @return mixed
     *
     */
    public function instantiate()
    {
        // Get the provider from the dependency list.
        $provider = $this->list->getObject(
            $this->providerIdentifier->get()
        );
        
        // Prepare list of arguments.
        $providerClass = get_class($provider);
        
        try
        {
            $providerMethod = new ReflectionMethod($providerClass, $this->method);
        }
        catch (ReflectionException $exception)
        {
            $message = $exception->getMessage();
            $identifierString = $this->identifier->get();
            throw new RuntimeException("Cannot call provider method {$this->method} on class {$providerClass} as provider for {$identifierString}. Method does not exist.");
        }
        
        $parameters = $providerMethod->getParameters();
        $invokeArgs = array();
        
        if (empty($parameters))
        {
            $object = $provider->{$this->method}();
            return $this->check($object, $providerClass);
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
                $identifier = $this->identifier->get();
                throw new RuntimeException("Provider injector failed to call {$this->method} on provider {$providerClass}. Required argument {$paramName} is not given at manual override. Please check your configuration.");
            }
        }
        
        $object = $this->callProviderMethod(
            $provider,
            $providerMethod,
            $invokeArgs
        );
        
        return $this->check($object, $providerClass);
    }
    
    /**
     * Check to make sure that the given object's class is correct.
     * Throws exception if it doesn't.
     * 
     * @param mixed $object
     * @param string $providerClass
     * @return mixed
     *
     */
    private function check($object, $providerClass)
    {
        if ($this->identifier->checkClass($object) == FALSE)
        {
            $identifierString = $this->identifier->get();
            $returnType = get_class($object);
            throw new RuntimeException("Method {$this->method} on class {$providerClass} does not return the right type for {$identifierString}, {$returnType} is returned instead.");
        }
        
        return $object;
    }
    
    /**
     * Calls the provider method and returns the returned value.
     * 
     * NOTE: Yes yes, I know this is ugly, but unfortunately
     * reflection is slow. So bear the ugliness.
     * 
     * @param mixed $provider
     * @param ReflectionMethod $providerMethod
     * @param array $invokeArgs
     *
     */
    private function callProviderMethod(
        $provider,
        $providerMethod,
        array $invokeArgs
    )
    {
        $count = count($invokeArgs);
        $method = $this->method;
        
        switch ($count)
        {
            case 0:
                return $provider->{$this->method}();
            break;
            case 1:
                return $provider->{$this->method}($invokeArgs[0]);
            break;
            case 2:
                return $provider->{$this->method}(
                    $invokeArgs[0],
                    $invokeArgs[1]
                );
            break;
            case 3:
                return $provider->{$this->method}(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2]
                );
            break;
            case 4:
                return $provider->{$this->method}(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3]
                );
            break;
            case 5:
                return $provider->{$this->method}(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3],
                    $invokeArgs[4]
                );
            break;
            case 6:
                return $provider->{$this->method}(
                    $invokeArgs[0],
                    $invokeArgs[1],
                    $invokeArgs[2],
                    $invokeArgs[3],
                    $invokeArgs[4],
                    $invokeArgs[5]
                );
            break;
            case 7:
                return $provider->{$this->method}(
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
                return $provider->{$this->method}(
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
                return $provider->{$this->method}(
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
                return $provider->{$this->method}(
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
        
        return $providerMethod->invokeArgs(
            $object,
            $invokeArgs
        );
    }
}