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
 * Validator Chain
 *
// ---------------------------------------------------------------
 * This class 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation;

class ValidatorChain
{
    /**
     * @var array List of values to be validated.
     */
    protected $values;
    
    /**
     * Constructor.
     *
     * @param 
     *
     */
    public function __construct()
    {
        $this->values = array();
    }
    
    public function setValues(array $values)
    {
        
    }
}