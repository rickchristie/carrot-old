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
 * Form Renderer Interface
 * 
// ---------------------------------------------------------------
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form;

interface FormViewInterface
{
    public function __construct(FormDefinition $form);
    
    public function enctype();
    
    public function method();
    
    public function render();
    
    public function fieldsets();
    
    public function fieldset($label);
    
    public function fieldsetOpen($label);
    
    public function fieldsetClose($label);
    
    public function fields();
    
    public function fieldsNotInFieldsets();
    
    public function field($fieldID);
    
    public function beforeField($fieldID = '');
    
    public function fieldLabel($fieldID);
    
    public function fieldControl($fieldID);
    
    public function fieldErrors($fieldID);
    
    public function afterField($fieldID = '');
}