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
 * Password Field
 * 
// ---------------------------------------------------------------
 * Value object
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

class PasswordField extends AbstractSingleInputField
{
    protected $type = 'checkbox';
    
    protected $forbidden = array('name', 'type', 'value', 'id', 'checked');
    
    protected $checked = FALSE;
    
    public function getValue(array $formSubmissionArray)
    {
        if (array_key_exists($this->name, $formSubmissionArray))
        {
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function setDefaultValue($defaultValue)
    {
        $this->checked = (bool) $defaultValue;
    }
    
    public function isSubmissionValid(array $formSubmissionArray)
    {
        return TRUE;
    }
    
    public function renderControl()
    {
        $attributes = $this->attributes->render();
        $name = htmlentities($this->name, ENT_QUOTES);
        $type = htmlentities($this->value, ENT_QUOTES);
        $checked = '';
        
        if ($this->checked)
        {
            $checked = ' checked="checked"';
        }
        
        return "<input type=\"{$type}\" value=\"1\" id=\"{$name}\" name=\"{$name}\"{$checked}{$attributes} />";
    }
}