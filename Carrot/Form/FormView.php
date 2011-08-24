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

use Carrot\Form\Renderer\FieldRenderer;
use Carrot\Form\Renderer\FieldRendererInterface;
use Carrot\Form\Renderer\FieldsetRenderer;
use Carrot\Form\Renderer\FieldsetRendererInterface;

class FormView implements FormViewInterface
{
    /**
     * @var type comments
     */
    protected $form;
    
    /**
     * @var type comments
     */
    protected $fieldRenderer;
    
    /**
     * @var type comments
     */
    protected $fieldsetRenderer;
    
    public function __construct(FormDefinition $form, FieldRendererInterface $fieldRenderer = NULL, FieldsetRendererInterface $fieldsetRenderer = NULL)
    {
        if ($fieldRenderer == NULL)
        {
            $fieldRenderer = new FieldRenderer;
        }
        
        if ($fieldsetRenderer == NULL)
        {
            $fieldsetRenderer = new FieldsetRenderer;
        }
        
        $this->form = $form;
        $this->fieldRenderer = $fieldRenderer;
        $this->fieldsetRenderer = $fieldsetRenderer;
    }
    
    /**
     * Render form encoding type.
     *
     */
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
    
    /**
     * Render fieldsets, along with parameters.
     * 
     * @return string 
     * 
     */
    public function fieldsets()
    {
        $fieldsetsRendered = '';
        
        foreach ($this->form->getFieldsets() as $label => $fields)
        {
            $fieldsetsRendered .= $this->fieldset($label);
        }
        
        return $fieldsetsRendered;
    }
    
    /**
     * Render a specific fieldset along with its parameters.
     *
     */
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
    
    /**
     * Render fieldset opening HTML.
     *
     */
    public function fieldsetOpen($label)
    {
        return $this->fieldsetRenderer->renderFieldsetOpen($label);
    }
    
    /**
     * Render fieldset closing HTML.
     *
     */
    public function fieldsetClose($label)
    {
        return $this->fieldsetRenderer->renderFieldsetClose($label);
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
        $field = $this->form->getField($fieldID);
        return $this->fieldRenderer->renderBeforeField($field);
    }
    
    public function fieldLabel($fieldID)
    {
        $field = $this->form->getField($fieldID);
        return $this->fieldRenderer->renderFieldLabel($field);
    }
    
    public function fieldControl($fieldID)
    {
        $field = $this->form->getField($fieldID);
        return $this->fieldRenderer->renderFieldControl($field);
    }
    
    public function fieldErrors($fieldID)
    {
        $field = $this->form->getField($fieldID);
        return $this->fieldRenderer->renderFieldErrors($field);
    }
    
    public function afterField($fieldID = '')
    {
        $field = $this->form->getField($fieldID);
        return $this->fieldRenderer->renderAfterField($field);
    }
}