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
 * Not Empty Validator
 * 
 * Used to validate the non-emptiness of a value. This is very
 * useful in cases where you want to make sure that a value is not
 * empty. Includes a ported Zend Framework 1.11 NotEmpty Validator
 * class logic.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation\Validator;

use Carrot\Message\ValidationErrorMessage;
use Carrot\Validation\ValidatorResult;

class NotEmptyValidator implements ValidatorInterface
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
            'notEmpty.simple' => array($this, 'simple'),
            'notEmpty.zend' => array($this, 'zend')
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
     * Value must not be empty.
     * 
     * Will return valid result if the value type is integer, float,
     * or boolean, as they always has a value. If the value type is
     * string, it will trimmed first before checking, if the string is
     * empty, will return invalid result.
     *
     * Otherwise for all other types, this method will use PHP's
     * empty() function to check.
     * 
     * @param string $valueID The ID of the value.
     * @param mixed $value The value to be validated.
     * 
     */
    public function simple($valueID, $value)
    {   
        if (is_integer($value) OR is_bool($value) OR is_float($value))
        {
            return $this->getValidResult();
        }
        
        if (is_string($value))
        {
            $value = trim($value);
            
            if ($value === '')
            {
                return $this->getInvalidResult($valueID);
            }
        }
        
        if (empty($value))
        {
            return $this->getInvalidResult($valueID);
        }
        
        return $this->getValidResult();
    }
    
    /**
     * Value must not be empty according to Zend Framework 1.11 NotEmpty rules.
     * 
     * This is effectively a port of Zend Framework's NotEmpty
     * validation class (v1.11). Ported because it is a swell class.
     * 
     * @see http://framework.zend.com/manual/en/zend.validate.set.html
     * @param string $valueID The ID of the value.
     * @param mixed $value The value to be validated.
     * @param array $args Array according to 
     *
     */
    public function zend($valueID, array $parameters, array $args = array())
    {
        // TODO: Finish porting!
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
        $this->message = array(
            'default' => _('{#label} must not be empty.')
        );
    }
    
    /**
     * Get a valid ValidatorResult instance.
     *
     * @return ValidationResult
     *
     */
    protected function getValidResult()
    {
        $result = new ValidatorResult(TRUE);
        return $result;
    }
    
    /**
     * Get an invalid ValidatorResult instance.
     *
     * @return ValidationResult
     * 
     */
    protected function getInvalidResult($valueID)
    {
        $message = new ValidationErrorMessage(
            get_class($this),
            $this->message['default']
        );
        
        $result = new ValidatorResult(FALSE);
        $result->addErrorMessage($message);
        return $result;
    }
}