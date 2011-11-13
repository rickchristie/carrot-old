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
 * Form Renderer Interface
 * 
 * This interface defines a contract between a rendering class and
 * the FormView helper class. By implementing this interface and
 * injecting your own implementation to the FormView instance, you
 * can modify the rendering behavior without altering the FormView
 * helper class.
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

use Carrot\Form\Field\FieldInterface;

interface FormRendererInterface
{
    /**
     * Render form error messages summary.
     * 
     * The form message summary is the list of error messages on the
     * top of the form commonly found in most forms. This method
     * renders it.
     * 
     * For more information about the arguments that will be sent by
     * FormView to this method,
     * {@see FormDefinition::getFieldValidationErrorMessages()} and
     * {@see FormDefinition::getFormValidationErrorMessages()}.
     * 
     * @param array $formErrors Errors generic to the form.
     * @param array $fieldErrors Errors generic to the field.
     * @return string The form errors, rendered in HTML.
     *
     */
    public function renderErrorMessagesSummary(array $formErrors, array $fieldErrors);
    
    /**
     * Render the whole field HTML.
     * 
     * A whole rendering of a field typically contains the field
     * control, the field label, and field errors. However, the
     * rendering may differ from one form theme to another. You may
     * want to disable rendering the field errors for example, and
     * depend on the summary instead.
     *
     * This method is called by {@see FormView::renderField()}, which
     * becomes the backbone of the automatic rendering feature.
     * 
     * @param FieldInterface $field The field object to be rendered.
     * @return string The whole field, rendered in HTML.
     *
     */
    public function renderField(FieldInterface $field);
    
    /**
     * Render field label.
     * 
     * You must check
     * {@see FieldInterface::shouldRendererRenderLabel()} before
     * rendering.
     * 
     * @param FieldInterface $field The field object to be rendered.
     * @return string The label, rendered in HTML.
     * 
     */
    public function renderFieldLabel(FieldInterface $field);
    
    /**
     * Render field control.
     * 
     * You must use {@see FieldInterface::render()}, since only the
     * field objects know how to render themselves into HTML strings.
     * 
     * @param FieldInterface $field The field object to be rendered.
     * @return string The field form control(s), rendered in HTML.
     * 
     */
    public function renderFieldControl(FieldInterface $field);
    
    /**
     * Render field errors.
     * 
     * You must check
     * {@see FieldInterface::shouldRendererRenderErrors()} before
     * rendering.
     * 
     * @param FieldInterface $field The field object to be rendered.
     * @return string The field specific errors, rendered in HTML.
     * 
     */
    public function renderFieldErrors(FieldInterface $field);
    
    /**
     * Render the opening tag of the fieldset.
     * 
     * @param string $legend The legend string of the fieldset.
     * @return string The fieldset opening tag.
     * 
     */
    public function renderFieldsetOpen($legend);
    
    /**
     * Render the closing tag of the fieldset.
     * 
     * @param string $legend The legend string of the fieldset.
     * @return string The fieldset closing tag.
     * 
     */
    public function renderFieldsetClose($legend);
}