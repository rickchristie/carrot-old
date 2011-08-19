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

abstract class AbstractSingleInputField implements FieldInterface
{
    /**
     * @var type comments
     */
    protected $id;
    
    /**
     * @var type comments
     */
    protected $name;
    
    protected $label;
    
    /**
     * @var Attributes comments
     */
    protected $attributes;
    
    /**
     * @var type comments
     */
    protected $forbidden = array('name', 'type', 'value', 'id');
    
    /**
     * @var string The input type 
     */
    protected $inputType = 'text';
    
    protected $defaultValue = '';
    
    protected $errorMessages = array();
    
    /**
     * Constructor.
     * 
     * @param array $attributes
     * 
     */
    public function __construct($id, $label, $prefix = '', array $attributes = array())
    {
        $this->id = $id;
        $this->label = $label;
        $this->name = $prefix . $id;
        $this->attributes = new Attributes($attributes, $this->forbidden);
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }
    
    public function getValue(array $formSubmissionArray)
    {
        if (array_key_exists($this->name, $formSubmissionArray))
        {
            return $formSubmissionArray[$this->name];
        }
        
        return NULL;
    }
    
    public function isSubmissionValid(array $formSubmissionArray)
    {
        if (array_key_exists($this->name, $formSubmissionArray))
        {
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function getID()
    {
        return $this->id;
    }
    
    public function getLabel()
    {
        return $this->label;
    }
    
    public function addErrorMessage($message)
    {
        $this->errorMessages[] = (string) $message;
    }
    
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }
    
    public function renderControl()
    {
        $attributes = $this->attributes->render();
        $defaultValue = htmlentities($this->defaultValue, ENT_QUOTES);
        $name = htmlentities($this->name, ENT_QUOTES);
        $type = htmlentities($this->type, ENT_QUOTES);
        return "<input type=\"{$type}\" id=\"{$name}\" name=\"{$name}\" value=\"{$defaultValue}\"{$attributes} />";
    }
    
    public function renderLabel()
    {
        $name = htmlentities($this->name, ENT_QUOTES);
        $label = htmlentities($this->label, ENT_QUOTES);
        return "<label for=\"{$name}\">{$label}</label>";
    }
}