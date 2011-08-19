<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Text Field
 * 
// ---------------------------------------------------------------
 * Value object
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

class TextField
{
    /**
     * @var type comments
     */
    protected $requestVariableName;
    
    /**
     * @var type comments
     */
    protected $attributes;
    
    /**
     * Constructor.
     * 
     * 
     * 
     */
    public function __construct(array $attributes = array())
    {
        
    }
    
    public function setRequestVariableName($requestVariableName)
    {
        $this->requestVariableName;
    }
    
    public function getRequestVariableValue(array $requestArray)
    {
        
    }
    
    public function setDefaultValue($defaultValue)
    {
        
    }
    
    public function render()
    {
        $attributes = $this->
        
        return '<input type="text"
    }
    
    public function getAttributes()
    {
        
    }
    
    protected function initializeAttributes(array $attributes = array())
    {
        $forbidden = array(
            
        );
    }
}