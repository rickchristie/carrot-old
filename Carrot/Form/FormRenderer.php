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
 * Form Renderer
 *
 * This is the default implementation for FormRendererInterface.
 * If you wanted to change FormView's behavior, you can create an
 * alternate implementation of FormRendererInterface and inject
 * your version to the FormView helper instead.
 * 
 * NOTE: FormView, FormRendererInterface and its default
 * implementation FormRenderer is not meant to be the end all be
 * all set of classes used to render the form. They serve as
 * helper classes only, and you are free (and even encouraged) to
 * create your own rendering classes, which makes use of
 * FormDefinition's API.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form;

use Carrot\Form\Field\FieldInterface;

class FormRenderer implements FormRendererInterface
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
     * @return string The form errors, rendered in HTML, safe for output.
     *
     */
    public function renderErrorMessagesSummary(array $formErrors, array $fieldErrors)
    {
        $summaryRendered = '';
        
        if (!empty($formErrors))
        {
            $summaryRendered .= '<ul class="formErrors">';
            
            foreach ($formErrors as $message)
            {
                $messageString = $message->get();
                $summaryRendered .= "<li>{$messageString}</li>";
            }
            
            $summaryRendered .= '</ul>';
        }
        
        if (!empty($fieldErrors))
        {
            $summaryRendered .= '<ul class="fieldErrors">';
            
            foreach ($fieldErrors as $messages)
            {
                foreach ($messages as $message)
                {
                    $messageString = $message->get();
                    $summaryRendered .= "<li>{$messageString}</li>";
                }
            }
            
            $summaryRendered .= '</ul>';
        }
        
        if (!empty($summaryRendered))
        {
            $summaryRendered =
                "<div class=\"formErrorSummary\">
                    {$summaryRendered}
                </div>";
        }
        
        return $summaryRendered;
    }
    
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
     * @return string The whole field, rendered in HTML, safe for output.
     *
     */
    public function renderField(FieldInterface $field)
    {
        $divClass = 'fieldContainer';
        $fieldClassName = get_class($field);
        $position = strripos($fieldClassName, '\\');
        
        if ($position !== FALSE)
        {
            $divClass .= ' ' . substr($fieldClassName, $position + 1);
        }
        
        if ($field->hasErrorMessages())
        {
            $divClass .= ' hasError';
        }
        
        $fieldRendered = "<div class=\"{$divClass}\">";
        $fieldRendered .= $this->renderFieldLabel($field);
        $fieldRendered .= $this->renderFieldControl($field);
        $fieldRendered .= $this->renderFieldErrors($field);
        $fieldRendered .= '</div>';
        return $fieldRendered;
    }
    
    /**
     * Render field label.
     * 
     * Checks {@see FieldInterface::shouldRendererRenderLabel()}
     * before rendering.
     * 
     * @param FieldInterface $field The field object to be rendered.
     * @return string The label, rendered in HTML, safe for output.
     * 
     */
    public function renderFieldLabel(FieldInterface $field)
    {
        if (!$field->shouldRendererRenderLabel())
        {
            return;
        }
        
        $label = $field->getLabel();
        $forAttribute = '';
        $HTMLID = $field->getForAttributeInLabel();
        
        if ($HTMLID != NULL)
        {
            $forAttribute = " for=\"{$HTMLID}\"";
        }
        
        return 
            "<div class=\"fieldLabelContainer\">
                <label{$forAttribute}>{$label}</label>
            </div>";
    }
    
    /**
     * Render field control.
     * 
     * You must use {@see FieldInterface::render()}, since only the
     * field objects know how to render themselves into HTML strings.
     * 
     * @param FieldInterface $field The field object to be rendered.
     * @return string The field form control(s), rendered in HTML, safe for output.
     * 
     */
    public function renderFieldControl(FieldInterface $field)
    {
        $controlRendered = $field->render();
        return
            "<div class=\"fieldControlContainer\">
                {$controlRendered}
            </div>";
    }
    
    /**
     * Render field errors.
     * 
     * Checks {@see FieldInterface::shouldRendererRenderErrors()}
     * before rendering.
     * 
     * @param FieldInterface $field The field object to be rendered.
     * @return string The field specific errors, rendered in HTML, safe for output.
     * 
     */
    public function renderFieldErrors(FieldInterface $field)
    {
        if (!$field->shouldRendererRenderErrors())
        {
            return;
        }
        
        $errors = $field->getErrorMessages();
        $errorsRendered = '<div class="fieldErrorsContainer"><ul>';
        
        foreach ($errors as $message)
        {
            $message = htmlentities($message->get(), ENT_QUOTES);
            $errorsRendered .= "<li>{$message}</li>";
        }
        
        $errorsRendered .= '</ul></div>';
        return $errorsRendered;
    }
    
    /**
     * Render the opening tag of the fieldset.
     * 
     * @param string $legend The legend string of the fieldset.
     * @return string The fieldset opening tag, safe for output.
     * 
     */
    public function renderFieldsetOpen($legend)
    {
        return "<fieldset><legend>{$legend}</legend>";
    }
    
    /**
     * Render the closing tag of the fieldset.
     * 
     * @param string $legend The legend string of the fieldset.
     * @return string The fieldset closing tag, safe for output.
     * 
     */
    public function renderFieldsetClose($legend)
    {
        return '</fieldset>';
    }
}