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
 * Form View
 * 
 * A helper class for automatic form rendering. Please note that
 * you can render easily without using this class, simply by
 * calling FormDefinition methods.
 *
 * NOTE: FormView, FormRendererInterface and its default
 * implementation FormRenderer is not meant to be the end all be
 * all set of classes used to render the form. They serve as
 * helper classes only, and you are free (and even encouraged) to
 * create your own rendering classes, which makes use of
 * FormDefinition's API.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form;

use RuntimeException,
    InvalidArgumentException,
    Carrot\Form\Field\FieldInterface;

class FormView
{
    /**
     * @var FormDefinition The form to be rendered.
     */
    protected $form;
    
    /**
     * @var FormRendererInterface The rendering engine used to render the form.
     */
    protected $renderer;
    
    /**
     * @var array Contains callbacks to override {@see renderField()}, along with their specific field IDs.
     */
    protected $overrides = array();
    
    /**
     * Constructor.
     * 
     * To construct the form view helper, you must pass an instance
     * of form definition. This helper also needs an implementation of
     * FormRendererInterface for most of the rendering work. If you
     * don't inject it at construction, the default implementation
     * will be used instead. 
     * 
     * <code>
     * $formView = new FormView($formDefinition, $formRenderer);
     * </code>
     * 
     * @see FormRenderer
     * @see FormRendererInterface
     * @param FormDefinition $form The form to be rendered.
     * @param FormRendererInterface $renderer The rendering engine used to render the form.
     * 
     */
    public function __construct(FormDefinition $form, FormRendererInterface $renderer = NULL)
    {
        if ($renderer == NULL)
        {
            $renderer = new FormRenderer;
        }
        
        $this->form = $form;
        $this->renderer = $renderer;
    }
    
    /**
     * Override FormRendererInterface::renderField() for a specific field.
     * 
     * In case there is one or two field whose rendering differs from
     * the rest, this method will save you time by allowing you to
     * override the rendering behavior for just the specific field you
     * wanted.
     *
     * Pass either an anonymous function or a valid array callback
     * (the object reference and the method name). The FieldInterface
     * object and the FormRendererInterface object will be sent as
     * arguments. Your callback's returned string will be directly
     * used as the result of {@see renderField}.
     *
     * <code>
     * $formView->overrideFieldRendering('username',
     *     function(FieldInterface $field, FormRendererInterface $renderer)
     *     {
     *         // Just render the control for
     *         // this particular field
     *         return $field->render();
     *     }
     * );
     * </code>
     *
     * This method will only affect methods that make use of
     * {@see renderField()}.
     * 
     * @param string $fieldID The ID of the field whose rendering you wanted to override.
     * @param callback $callback The callbak to return.
     * 
     */
    public function overrideFieldRenderer($fieldID, $callback)
    {
        if (!$this->form->fieldExists($fieldID))
        {
            throw new InvalidArgumentException("FormView error in setting field rendering override for '{$fieldID}'. The field does not exist.");
        }
        
        if (!is_callable($callback))
        {
            throw new InvalidArgumentException("FormView error in setting field rendering override for '{$fieldID}'. The callback given is not callable.");
        }
        
        $this->overrides[$fieldID] = $callback;
    }
    
    /**
     * Get the encoding type of the form.
     * 
     * @return string Used to fill 'enctype' attribute of the form opening tag.
     * 
     */
    public function getEnctype()
    {
        return $this->form->getEnctype();
    }
    
    /**
     * Get the method of the form.
     * 
     * @return string Used to fill 'method' attribute of the form opening tag.
     *
     */
    public function getMethod()
    {
        return $this->form->getMethod();
    }
    
    /**
     * Render all the form's contents (fieldsets and fields).
     * 
     * First renders all the fieldsets with {@see renderFieldsets()},
     * then renders the rest of the fields not grouped in any
     * fieldsets {@see renderFieldsNotInFieldsets()}.
     * 
     * Uses {@see renderField()} to render the fields, which means
     * your overriding renderer callback will affect the output of
     * this method.
     * 
     * @return string All the form fields/fieldsets rendered in HTML string.
     * 
     */
    public function renderAll()
    {
        $allRendered = $this->renderAllFieldsets();
        $allRendered .= $this->renderFieldsNotInFieldsets();
        return $allRendered;
    }
    
    /**
     * Render all the fields (including the ones grouped in fieldsets).
     * 
     * This method renders all fields, regardless of whether the
     * fields are grouped in a fieldset or not. If you call this
     * method but also call any of the fieldset rendering methods, it
     * will lead to duplication on the rendered fields.
     * 
     * Uses {@see renderField()} to render the fields, which means
     * your overriding renderer callback will affect the output of
     * this method.
     * 
     * @return string All the fields, rendered in HTML.
     * 
     */
    public function renderAllFields()
    {
        $fields = $this->form->getFields();
        $renderedFields = '';
        
        foreach ($fields as $fieldID => $field)
        {
            $renderedFields .= $this->renderField($fieldID);
        }
        
        return $renderedFields;
    }
    
    /**
     * Render FieldInterface instances that are not grouped in any fieldsets.
     * 
     * This method is used in conjunction with
     * {@see renderAllFieldsets()} to render the whole form. The logic
     * is to first render fieldsets, then render the rest of the
     * fields that are not grouped in fieldsets.
     * 
     * Uses {@see renderField()} to render the fields, which means
     * your overriding renderer callback will affect the output of
     * this method.
     *
     * @return string The fields, rendered in HTML.
     *
     */
    public function renderFieldsNotInFieldsets()
    {
        $fields = $this->form->getFieldsNotInFieldsets();
        $fieldsNotInFieldsetsRendered = '';
        
        foreach ($fields as $fieldID => $field)
        {
            $fieldsNotInFieldsetsRendered .= $this->renderField($fieldID);
        }
        
        return $fieldsNotInFieldsetsRendered;
    }
    
    /**
     * Render all fieldsets, along with their fields.
     * 
     * Uses {@see renderField()} to render the fields, which means
     * your overriding renderer callback will affect the output of
     * this method.
     * 
     * @return string All fieldsets, rendered in HTML.
     *
     */
    public function renderAllFieldsets()
    {
        $fieldsets = $this->form->getFieldsets();
        $fieldsetsRendered = '';
        
        foreach ($fieldsets as $legend => $fieldset)
        {
            $fieldsetsRendered .= $this->renderer->renderFieldsetOpen($legend);
            
            foreach ($fieldset as $fieldID => $field)
            {
                $fieldsetsRendered .= $this->renderField($fieldID);
            }
            
            $fieldsetsRendered .= $this->renderer->renderFieldsetClose($legend);
        }
        
    }
    
    /**
     * Render an entire fieldset, along with its fields.
     * 
     * Uses {@see renderField()} to render the fields, which means
     * your overriding renderer callback will affect the output of
     * this method.
     * 
     * @param string $legend The legend of the fieldset to be rendered.
     * @return string The entire fieldset rendered.
     *
     */
    public function renderFieldset($legend)
    {
        $fieldset = $this->form->getFieldset($legend);
        $fieldsetRendered = $this->renderer->renderFieldsetOpen($legend);
        
        foreach ($fieldset as $field)
        {
            $fieldID = $field->getID();
            $fieldsetRendered .= $this->renderField($fieldID);
        }
        
        $fieldsetRendered .= $this->renderer->renderFieldsetClose($legend);
        return $fieldsetRendered;
    }
    
    /**
     * Render the give field ID.
     * 
     * Will first check to see if there is an overriding renderer
     * callback registered to the given field ID. If it exists, will
     * run the callback, otherwise will use 
     * {@see FormRendererInterface::renderField()}.
     * 
     * @param string $fieldID The ID of the field to be rendered.
     * @return string The field, rendered in HTML.
     * 
     */
    public function renderField($fieldID)
    {
        $field = $this->form->getField($fieldID);
        
        if (array_key_exists($fieldID, $this->overrides))
        {
            $callback = $this->overrides[$fieldID];
            return $this->runRendererCallback($field, $callback);
        }
        
        return $this->renderer->renderField($field);
    }
    
    /**
     * Wraps {@see FormRendererInterface::renderErrorMessagesSummary()}.
     *
     * @return string The error message summary, rendered in HTML.
     *
     */
    public function renderErrorMessagesSummary()
    {
        $formErrors = $this->form->getFormValidationErrorMessages();
        $fieldErrors = $this->form->getFieldValidationErrorMessages();
        return $this->renderer->renderErrorMessagesSummary(
            $formErrors,
            $fieldErrors
        );
    }
    
    /**
     * Wraps {@see FormRendererInterface::renderFieldLabel()}.
     *
     * @param string $fieldID The ID of the field to be rendered.
     * @return string The field label, rendered in HTML.
     *
     */
    public function renderFieldLabel($fieldID)
    {
        $field = $this->form->getField($fieldID);
        return $this->renderer->renderFieldLabel($field);
    }
    
    /**
     * Wraps {@see FormRendererInterface::renderFieldControl()}.
     *
     * @param string $fieldID The ID of the field to be rendered.
     * @return string The field control, rendered in HTML.
     *
     */
    public function renderFieldControl($fieldID)
    {
        $field = $this->form->getField($fieldID);
        return $this->renderer->renderFieldControl($field);
    }
    
    /**
     * Wraps {@see FormRendererInterface::renderFieldErrors()}.
     *
     * @param string $fieldID The ID of the field to be rendered.
     * @return string The field specific errors, rendered in HTML.
     *
     */
    public function renderFieldErrors($fieldID)
    {
        $field = $this->form->getField($fieldID);
        return $this->renderer->renderFieldErrors($field);
    }
    
    /**
     * Wraps {@see FormRendererInterface::renderFieldsetOpen()}.
     *
     * @param string $legend The legend of the fieldset to be rendered.
     * @return string The fieldset opening tag, in HTML.
     *
     */
    public function renderFieldsetOpen($legend)
    {
        return $this->renderer->renderFieldsetOpen($legend);
    }
    
    /**
     * Wraps {@see FormRendererInterface::renderFieldsetClose()}.
     *
     * @param string $legend The legend of the fieldset to be rendered.
     * @return string The fieldset closing tag, in HTML.
     *
     */
    public function renderFieldsetClose($legend)
    {
        return $this->renderer->renderFieldsetClose($legend);
    }
    
    /**
     * Calls the overriding renderer callback.
     * 
     * If the callback is an array, will assume that it's an object
     * and method name. Static callbacks are not allowed. Otherwise
     * it will assume that it's an anonymous function.
     * 
     * @param FieldInterface $field The field to be rendered.
     * @param callback $callback The overriding renderer callback.
     * @return string The returned string from the renderer callback.
     * 
     */
    protected function runRendererCallback(FieldInterface $field, $callback)
    {
        if (is_array($callback))
        {
            if (count($callback) != 2 OR !is_object($callback[0]))
            {
                throw new RuntimeException("FormView error in running overriding renderer callback. The callback must be either anonymous function or an array of object reference and method name (static calls not allowed).");
            }
            
            $object = $callback[0];
            $method = $callback[1];
            return $object->$method(
                $field,
                $this->renderer
            );
        }
        
        return $callback(
            $field,
            $this->renderer
        );
    }
}