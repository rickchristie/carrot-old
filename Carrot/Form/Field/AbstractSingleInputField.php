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
 * Abstract Input Field
 * 
// ---------------------------------------------------------------
 * Input fields are defined as form <input> tags, with the
 * important characteristic of storing the request variable name
 * in 'name' attribute and default value in 'value' attribute.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

use Carrot\Form\Attributes;

abstract class AbstractSingleInputField implements FieldInterface
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
     * @var type comments
     */
    protected $forbidden = array('name', 'type');
    
    /**
     * @var string The input type 
     */
    protected $inputType = 'text';
    
    /**
     * Constructor.
     * 
     * @param array $attributes
     * 
     */
    public function __construct(array $attributes = array())
    {
        $this->initializeAttributes($attributes);
    }
    
    /**
     * Render the field
     * 
     */
    public function render()
    {
        
    }
    
    public function setRequestVariableName($requestVariableName)
    {
        $this->requestVariableName = $requestVariableName;
        $attributes = $this->attributes->getAll();
        $attributes['name'] = $requestVariableName;
        $this->initializeAttributes($attributes);
    }
    
    public function getRequestVariableValue(array $requestArray)
    {
        if (!array_key_exists($this->requestVariableName, $requestArray))
        {
            return NULL;
        }
        
        return $requestArray($requestVariableName);
    }
    
    public function setDefaultValue($defaultValue)
    {
        $this->attributes->set('value', $defaultValue);
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    protected function initializeAttributes(array $attributes = array())
    {
        $this->attributes = new Attributes(
            $attributes,
            $this->forbidden
        );
    }
}