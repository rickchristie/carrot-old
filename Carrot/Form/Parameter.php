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
 * Parameter
 * 
// ---------------------------------------------------------------
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form;

class Parameter
{
    /**
     * @var string The ID of this parameter.
     */
    protected $id;
    
    /**
     * @var string The label string of this parameter.
     */
    protected $label;
    
    /**
     * @var string The index in the request variable that stores this parameter's value.
     */
    protected $requestVariableName;
    
    /**
     * @var FieldInterface The HTML representation of this parameter.
     */
    protected $field;
    
    /**
     * @var array Array of error messages attached to this parameter, in string. 
     */
    protected $errorMessages;
    
    /**
     * Constructor.
     * 
     * 
     * 
     * @param string $id The ID of this parameter.
     * @param string $label The label of this parameter.
     * @param FieldInterface $field The form representation of this parameter.
     * 
     */
    public function __construct($id, $label, $requestVariableName, FieldInterface $field)
    {
        $this->id = $id;
        $this->label = $label;
        $this->requestVariableName = $requestVariableName;
        $this->field = $field;
    }
    
    /**
     * Returns the ID of this parameter.
     * 
     * @return string
     *
     */
    public function getID()
    {
        return $this->id;
    }
    
    /**
     * Returns the label of this paramter.
     *
     * @return string
     *
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * Returns the request variable name for this attribute.
     * 
     * @return string
     * 
     */
    public function getRequestVariableName()
    {
        return $this->requestVariableName;
    }
    
    /**
     * Get the form representation of this parameter.
     * 
     * @return FieldInterface
     *
     */
    public function getField()
    {
        return $this->field;
    }
    
    /**
     * Attach an error message string to this parameter.
     * 
     * @param string $message The message in string.
     *
     */
    public function addErrorMessage($message)
    {
        $this->messages[] = $message;
    }
}