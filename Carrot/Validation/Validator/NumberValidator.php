<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Number Validator
 * 
 * Validates a number.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation\Validator;

use Carrot\Validation\ValidatorResult;

class NumberValidator implements ValidatorInterface
{
    /**
     * @var array List of validator IDs and their callbacks.
     */
    protected $callbacks;
    
    /**
     * @var array Contains message strings to be used when the value is empty.
     */
    protected $messages;
    
    /**
     * Constructor.
     *
     * Initializes $callbacks and $messages class property.
     *
     */
    public function __construct()
    {
        $this->callbacks = array(
            
        );
        
        $this->initializeMessages();
    }
    
    /**
     * Get callbacks.
     *
     * @return array List of validator IDs and their callbacks.
     *
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }
    
    /**
     * Initialize messages.
     * 
     * By initializing the messages in this method, we can use
     * gettext's _() shorthand function, while also allowing users to
     * extend and override this method to change the messages without
     * having to change anything else in the class.
     * 
     */
    protected function initializeMessages()
    {
        $this->messages = array(
        
        );
    }
}