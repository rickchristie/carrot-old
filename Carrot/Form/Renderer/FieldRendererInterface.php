<?php

namespace Carrot\Form\Renderer;

use Carrot\Form\Field\FieldInterface;

interface FieldRendererInterface
{
    public function renderBeforeField(FieldInterface $field);
    
    public function renderFieldLabel(FieldInterface $field);
    
    public function renderFieldControl(FieldInterface $field);
    
    public function renderFieldErrors(FieldInterface $field);
    
    public function renderAfterField(FieldInterface $field);
}