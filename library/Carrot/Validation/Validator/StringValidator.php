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
 * String Validator
 * 
 * Used to validate attributes of a string value. Note that this
 * validator group doesn't cast the value to string before
 * validating. You can use Type validator to cast the value into
 * string before validating.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation\Validator;

use Carrot\Message\ValidationErrorMessage;
use Carrot\Validation\ValidatorResult;

class StringValidator implements ValidatorInterface
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
            'string.maxLength' => array($this, 'maxLength'),
            'string.minLength' => array($this, 'minLength'),
            'string.exactLength' => array($this, 'exactLength'),
            'string.isNumeric' => array($this, 'isNumeric'),
            'string.isAlpha' => array($this, 'isAlpha'),
            'string.isAlphaNumeric' => array($this, 'isAlphanumeric'),
            'string.mustMatchRegex' => array($this, 'mustMatchRegex'),
            'string.mustNotMatchRegex' => array($this, 'mustNotMatchRegex')
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
     * String length must not be more than the provided value.
     * 
     * @param string $valueID The ID of the value.
     * @param mixed $value The value to be validated.
     * @param int $maxLength The maximum string length.
     *
     */
    public function maxLength($valueID, $value, $maxLength)
    {      
        if (strlen($value) > $maxLength)
        {   
            $message = new ValidationErrorMessage(
                get_class($this),
                $this->messages['maxLength']
            );
            
            $message->setPlaceholder('maxLength', $maxLength);
            return $this->getInvalidResult($message);
        }
        
        return $this->getValidResult();
    }
    
    /**
     * String length must not be less than the provided value.
     * 
     * @param string $valueID The ID of the value.
     * @param mixed $value The value to be validated.
     * @param int $minLength The minimum string length.
     *
     */
    public function minLength($valueID, $value, $minLength)
    {
        // TODO: Implement minLength!
    }
    
    /**
     * String length must exactly the same as the provided value.
     * 
     * @param string $valueID The ID of the value.
     * @param mixed $value The value to be validated.
     * @param int $exactLength The exact string length.
     *
     */
    public function exactLength($valueID, $value, $exactLength)
    {
        // TODO: Implement exactLength!
    }
    
    /**
     * String must only contain numeric characters.
     * 
     * @param string $valueID The ID of the value.
     * @param mixed $value The value to be validated.
     *
     */
    public function isNumeric($valueID, $value)
    {
        // TODO: Implement isNumeric!
    }
    
    /**
     * String must only contain alphabetical characters.
     * 
     * @param string $valueID The ID of the value.
     * @param mixed $value The value to be validated.
     *
     */
    public function isAlpha($valueID, $value)
    {
        // TODO: Implement isAlpha!
    }
    
    /**
     * String must only contain alphabetical and numerical characters.
     * 
     * @param string $valueID The ID of the value.
     * @param mixed $value The value to be validated.
     *
     */
    public function isAlphaNumeric($valueID, $value)
    {
        // TODO: Implement isAlphaNumeric!
    }
    
    /**
     * String must match the regex provided.
     * 
     * @param string $valueID The ID of the value.
     * @param mixed $value The value to be validated.
     * @param string $regex The regular expression to be matched against the value.
     *
     */
    public function mustMatchRegex($valueID, $value, $regex)
    {
        // TODO: Implement mustMatchRegex!
    }
    
    /**
     * String must NOT match the regex provided.
     * 
     * @param string $valueID The ID of the value.
     * @param mixed $value The value to be validated.
     * @param string $regex The regular expression to be matched against the value.
     *
     */
    public function mustNotMatchRegex($valueID, $value, $regex)
    {
        // TODO: Implement mustNotMatchRegex!
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
            'maxLength' => _('{#label} must not be more than {:maxLength} characters length.')
        );
    }
    
    /**
     * Get a valid ValidationResult instance.
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
     * Get invalid ValidationResult instance.
     *
     * @return ValidationResult
     *
     */
    protected function getInvalidResult(ValidationErrorMessage $message)
    {
        $result = new ValidatorResult(FALSE);
        $result->addErrorMessage($message);
        return $result;
    }
}