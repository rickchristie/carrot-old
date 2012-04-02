<?php

namespace Carrot\Autopilot\Setter;

/**
//---------------------------------------------------------------
 * There is only one setter since 
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class RegularSetter implements SetterInterface
{
    /**
     * List of methods to be run, along with their arguments.
     * Structure of the array is as follows:
     * 
     * <pre>
     * $methods = array(
     *     'methodName' => array(
     *         'stringArgument',
     *         $autopilotReference,
     *         ...
     *     ),
     *     ...
     * );
     * </pre>
     * 
     * Like instantiator constructor arguments, you can use Autopilot
     * reference to refer to an instance.
     * 
     * @var array $methods
     */
    private $methods = array();
    
    /**
    //---------------------------------------------------------------
     * Adds a method to be run.
     *
     */
    public function addMethod($methodName, array $args)
    {
        
    }
}