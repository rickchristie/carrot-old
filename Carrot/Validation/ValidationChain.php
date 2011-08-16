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
 * Validation Chain
 *
 * Represents chains of validation processes. This class is used
 * to validate a set of parameters and to construct appropriate
 * error messages afterwards. It uses ErrorMessage from the
 * Message library to represent the error message. The resulting
 * error message should be tied to their respective parameter
 * IDs, which can then be passed to the Form library to update
 * the form definition with relevant error messages. This class
 * is meant to be used inside your model method.
 * 
 * You can either construct this object directly on your model
 * method (thereby encapsulating the validation behavior in your
 * domain model class) or you can have it injected to your model,
 * whichever suits you better.
 * 
// ---------------------------------------------------------------
 * asdf
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation;

use Carrot\Message\ErrorMessage;

class ValidationChain
{
    /**
     * @var array List of parameters to be validated, sorted by their parameter ID.
     */
    protected $parameters;
    
    /**
     * Constructor.
     * 
    // ---------------------------------------------------------------
     * 
     * 
     * @param array $basicValidators Basic validators in array.
     *
     */
    public function __construct(array $basicValidators = array(), array $complexValidators())
    {
        $this->values = array();
        $this->initialize
    }
    
    /**
     * Sets the values to validate.
     * 
    // ---------------------------------------------------------------
     * 
     * 
     * @param array $parameters List of parameters to be validated, sorted by their parameter ID.
     * 
     */
    public function setParameters(array $parameters)
    {
        
    }
}