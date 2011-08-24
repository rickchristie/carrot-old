<?php

namespace Carrot\Form\Renderer;

interface FieldsetRendererInterface
{
    public function renderFieldsetOpen($label);
    
    public function renderFieldsetClose($label);
}