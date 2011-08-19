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
 * Form View
 * 
// ---------------------------------------------------------------
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form;

class FormRenderer implements FormRendererInterface
{
    protected $form;
    
    public function __construct(FormDefinition $form)
    {
        $this->form = $form;
    }
    
    public function enctype()
    {
        return $this->form->getEnctype();
    }
    
    public function method()
    {
        return $this->form->getMethod();
    }
    
    /**
     * Render everything
     *
     */
    public function render()
    {
        $fieldsets = $this->fieldsets();
        $fields = $this->fieldsNotInFieldsets();
        return $fieldsets . $fields;
    }
    
    public function fieldsets()
    {
        $fieldsetsRendered = '';
        
        foreach ($this->form->getFieldsets() as $label => $fields)
        {
            $fieldsetsRendered .= $this->fieldset($label);
        }
        
        return $fieldsetsRendered;
    }
    
    public function fieldset($label)
    {
        $fieldsetRendered = $this->fieldsetOpen($label);
        
        foreach ($this->getFieldset($label) as $field)
        {
            $fieldsetRendered .= $this->field($field->getID());
        }
        
        $fieldsetRendered .= $this->fieldsetClose($label);
        return $fieldsetRendered;
    }
    
    public function fieldsetOpen($label)
    {
        return "<fieldset><legend>{$label}</legend>";
    }
    
    public function fieldsetClose($label)
    {
        return '</fieldset>';
    }
    
    public function fields()
    {
        $fieldsRendered = '';
        
        foreach ($this->form->getFields() as $fieldID => $field)
        {
            $fieldsRendered .= $this->field($fieldID);
        }
        
        return $fieldsRendered;
    }
    
    public function fieldsNotInFieldsets()
    {
        $fieldsRendered = '';
        
        foreach ($this->form->getFieldsNotInFieldsets() as $fieldID => $field)
        {
            $fieldsRendered .= $this->field($fieldID);
        }
        
        return $fieldsRendered;
    }
    
    public function field($fieldID)
    {
        $fieldRendered = $this->beforeField($fieldID);
        $fieldRendered .= $this->fieldLabel($fieldID);
        $fieldRendered .= $this->fieldControl($fieldID);
        $fieldRendered .= $this->fieldErrors($fieldID);
        $fieldRendered .= $this->afterField($fieldID);
        return $fieldRendered;
    }
    
    public function beforeField($fieldID = '')
    {
        return '<div class="fieldContainer">';
    }
    
    public function fieldLabel($fieldID)
    {
        $field = $this->form->getField($fieldID);
        $labelRendered = $field->renderLabel();
        return "<div class=\"fieldLabelContainer\">{$labelRendered}</div>";
    }
    
    public function fieldControl($fieldID)
    {
        $field = $this->form->getField($fieldID);
        $controlRendered = $field->renderControl();
        return "<div class=\"fieldControlContainer\">{$controlRendered}</div>";
    }
    
    public function fieldErrors($fieldID)
    {
        $field = $this->form->getField($fieldID);
        $errors = $field->getErrorMessages();
        $errorsRendered = '<div class="fieldErrorsContainer"><ul>';
        
        foreach ($errors as $message)
        {
            $message = htmlentities($message, ENT_QUOTES);
            $errorsRendered .= "<li>{$message}</li>";
        }
        
        $errorsRendered .= '</ul></div>';
        return $errorsRendered;
    }
    
    public function afterField($fieldID = '')
    {
        return '</div>';
    }
}