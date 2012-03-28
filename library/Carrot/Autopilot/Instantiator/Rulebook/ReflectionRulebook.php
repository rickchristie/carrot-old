<?php

namespace Carrot\Autopilot\Rulebook;

use ReflectionClass,
    Carrot\Autopilot\Context,
    Carrot\Autopilot\Instantiator\CtorInstantiator;

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
     * List of saved context objects, with context string as their
     * indexes.
     * 
     * @var array $contexts
     */
    private $contexts = array();
    
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
        if (array_key_exists($contextString, $this->contexts) == FALSE)
        {
            $this->contexts[$contextString] = new Context($contextString);
        }
        
        $this->defaultValues[$contextString][$varName] = $value;
    }
    
    /**
     * Automatically generate instantiator with the aid of reflection,
     * will return FALSE if unable to do so.
     * 
     * @param Reference $reference Autopilot reference to the
     *        instance we wanted to get.
     * @return CtorInstantiator|FALSE
     *
     */
    public function getInstantiator(Reference $reference)
    {
        $ctorDefaultArgs = $this->generateCtorDefaultArgs($reference);
        $ctorArgs = $this->generateCtorArgs(
            $ctorDefaultArgs,
            $reference
        );
        
        if (is_array($ctorArgs) == FALSE)
        {
            return FALSE;
        }
        
        return new CtorInstantiator($ctorArgs);
    }
    
    /**
     * Generate default constructor arguments for the given Autopilot
     * reference.
     * 
     * @param Reference $reference
     * @return array
     *
     */
    private function generateCtorDefaultArgs(Reference $reference)
    {
        $ctorDefaultArgs = array();
        
        foreach ($this->contexts as $contextString => $context)
        {
            if ($context->includes($reference))
            {
                foreach ($this->defaultValues[$contextString] as $varName => $value)
                {
                    if (array_key_exists($varName, $ctorDefaultArgs))
                    {
                        $oldContext = $ctorDefaultArgs[$varName]['context'];
                        
                        if ($context->isMoreSpecificThan($oldContext) == FALSE)
                        {
                            continue;
                        }
                    }
                    
                    $ctorDefaultArgs[$varName] = array(
                        'value' => $value,
                        'context' => $context
                    );
                }
            }
        }
        
        return $ctorDefaultArgs;
    }
    
    /**
     * Generates constructor arguments to use with both default
     * arguments and the given class name.
     * 
     * @param array $ctorDefaultArgs
     * @param Reference $reference
     *
     */
    private function generateCtorArgs(array $ctorDefaultArgs, Reference $reference)
    {
        $className = $reference->getClassName();
        $id = $reference->getId();
        
        if (method_exists($className, '__construct') == FALSE)
        {
            // No constructor, so it should be safe to
            // instantiate the class without arguments.
            return array();
        }
        
        $reflectionClass = new ReflectionClass($className);
        
        // We can't instantiate an interface.
        if ($reflectionClass->isInterface())
        {
            return FALSE;
        }
        
        $method = new ReflectionMethod($className, '__construct');
        $parameters = $method->getParameters();
        $ctorArgs = array();
        
        foreach ($parameters as $param)
        {
            $paramName = $param->getName();
            $paramClass = $param->getClass();
            
            // If a default argument exist, use it.
            if (array_key_exists($paramName, $ctorDefaultArgs))
            {
                $ctorArgs[] = $ctorDefaultArgs[$paramName];
                continue;
            }
            
            // If the parameter is a class, create unnamed
            // Autopilot reference as the default argument.
            if ($paramClass instanceof ReflectionClass)
            {
                $ctorArgs[] = new Reference($paramClass);
                continue;
            }
            
            // The parameter is not a class and there is
            // no predefined default value for it. We
            // check if a default value is provided at the
            // object's constructor.
            if ($param->isDefaultValueAvailable())
            {
                $ctorArgs[] = $param->getDefaultValue();
            }
            
            // Sorry bro, we tried our best here.
            // TODO: Don't forget to log this as 'Unable to generate...'.
            return FALSE;
        }
        
        return $ctorArgs;
    }
}