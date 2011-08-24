<?php

namespace Carrot\Form\Renderer;

use Carrot\Form\Field\FieldInterface;

class FieldRenderer implements FieldRendererInterface
{
    public function renderBeforeField(FieldInterface $field)
    {
        return '<div class="fieldContainer">';
    }
    
    public function renderFieldLabel(FieldInterface $field)
    {
        $labelRendered = $field->renderLabel();
        return "<div class=\"fieldLabelContainer\">{$labelRendered}</div>";
    }
    
    public function renderFieldControl(FieldInterface $field)
    {
        $controlRendered = $field->renderControl();
        return "<div class=\"fieldControlContainer\">{$controlRendered}</div>";
    }
    
    public function renderFieldErrors(FieldInterface $field)
    {
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
    
    public function renderAfterField(FieldInterface $field)
    {
        return '</div>';
    }
}