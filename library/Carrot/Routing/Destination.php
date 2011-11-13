<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Destination.
 *
 * The Destination class contains a Reference to the class Carrot
 * needs to instantiate, the method that Carrot should call, and
 * the arguments Carrot should pass when calling the method. It
 * is the object that the Router should return as the byproduct
 * of routing.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Routing;

use Carrot\DependencyInjection\Reference;

class Destination
{
    /**
     * @var Reference Refers to the object that Carrot needs to
     *      instantiate to generate the response.
     */
    protected $reference;
    
    /**
     * @var string The name of the method that Carrot needs to call
     *      to generate the response.
     */
    protected $methodName;
    
    /**
     * @var array The arguments that Carrot should pass to the method
     *      when calling it to generate the response.
     */
    protected $args;
    
    /**
     * Constructor.
     * 
     * The arguments array will be passed to the method according to
     * the rules of call_user_func_array().
     * 
     * @param Reference $reference Refers to the object that Carrot
     *        needs to instantiate to generate the response.
     * @param string $methodName The name of the method that Carrot
     *        needs to call to generate the response.
     * @param array $args The arguments that Carrot should pass to
     *        the method when calling it to generate the response.
     *
     */
    public function __construct(Reference $reference, $methodName, array $args = array())
    {
        $this->reference = $reference;
        $this->methodName = $methodName;
        $this->args = $args;
    }
    
    /**
     * Get the reference to the object that Carrot needs to
     * instantiate to generate the response.
     *
     * @param Reference $reference
     *
     */
    public function getReference()
    {
        return $this->reference;
    }
    
    /**
     * Get the name of the method that Carrot needs to call to
     * generate the response.
     *
     * @param string $methodName
     *
     */
    public function getMethodName()
    {
        return $this->methodName;
    }
    
    /**
     * Get the The arguments that Carrot should pass to the method
     * when calling it to generate the response.
     *
     * @param array $args
     *
     */
    public function getArgs()
    {
        return $this->args;
    }
}