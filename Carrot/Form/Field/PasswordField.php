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
 * Password Field
 * 
// ---------------------------------------------------------------
 * Value object
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

class PasswordField extends AbstractSingleInputField
{
    protected $type = 'password';
    
    public function setDefaultValue($defaultValue)
    {
        // This method is intentionally left blank
    }
}