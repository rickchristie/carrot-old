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
 * Text Field
 * 
// ---------------------------------------------------------------
 * Value object
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

class TextField extends AbstractSingleInputField
{
    protected $type = 'hidden';
    
    public function renderLabel()
    {
        // This method is intentionally left blank
    }
}