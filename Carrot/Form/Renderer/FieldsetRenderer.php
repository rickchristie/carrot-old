<?php

namespace Carrot\Form\Renderer;

class FieldsetRenderer implements FieldsetRendererInterface
{
    public function renderFieldsetOpen($label)
    {
        return "<fieldset><legend>{$label}</legend>";
    }
    
    public function renderFieldsetClose($label)
    {
        return '</fieldset>';
    }
}