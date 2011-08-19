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
 * Form View Interface
 * 
// ---------------------------------------------------------------
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form;

interface FormRendererInterface
{
    /**
     * Constructor.
     * 
     * 
     * 
     * @param FormDefinition $form 
     *
     */
    public function __construct(FormDefinition $form);
    
    public function parameterLabel();
    
    public function parameterErrors();
    
    public function parameterField();
    
    public function parameter();
    
    public function open();
    
    public function close();
    
    public function fieldsetOpen();
    
    public function fieldsetClose();
    
    
}