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
 * Validator Result
 *
 * Value object. Represents a result status from a validator
 * callback. The validator callback is to return this object when
 * called. You can pass an implementation of
 * {@see ValidationErrorMessageInterface} as an error message.
 * 
 * To return a valid result after validation:
 *
 * <code>
 * $result = new ValidatorResult(TRUE);
 * return $result;
 * </code>
 *
 * You cannot add an error message if the result is valid. Any
 * calls to {@see addErrorMessage} when the type is valid will be
 * ignored.
 * 
 * To return a invalid result after validation:
 * 
 * <code>
 * $result = new ValidatorResult(FALSE);
 * $result->addErrorMessage($message);
 * return $result;
 * </code>
 *
 * If your validator method transforms/cleans the validated value,
 * e.g. normalizing a date string, don't forget to put the new
 * value into the object:
 * 
 * <code>
 * $result->setValue('date', $formattedDate);
 * </code> 
 *
 * The ValidationChain object will check for new value using
 * {@see hasNewValue} after it receives a ValidatorResult object
 * and act accordingly.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation;

use Carrot\Message\ValidationErrorMessageInterface;

class ValidatorResult
{
    /**
     * @var bool If TRUE, then the value passes validation, FALSE otherwise.
     */
    protected $valid;
    
    /**
     * @var array Contains ValidationErrorMessageInterface instances.
     */
    protected $errorMessages = array();
    
    /**
     * @var array Contains the new value, transformed/cleaned by the validator.
     */
    protected $value = array();
    
    /**
     * @var bool TRUE if {@see setValue()} has been called, FALSE otherwise.
     */
    protected $hasNewValue = FALSE;
    
    /**
     * Constructor.
     * 
     * Specify whether the result is valid or not by passing a boolean
     * value on object construction:
     * 
     * <code>
     * $validResult = new ValidatorResult(TRUE);
     * $invalidResult = new ValidatorResult(FALSE);
     * </code>
     * 
     * @param bool $valid TRUE if passes validation, FALSE otherwise.
     *
     */
    public function __construct($valid)
    {
        $this->valid = (bool) $valid;
    }
    
    /**
     * Returns TRUE if passes validation, FALSE otherwise.
     * 
     * @return bool
     * 
     */
    public function isValid()
    {
        return $this->valid;
    }
    
    /**
     * Set the new value, transformed/cleaned by the validator.
     * 
     * This is optional, since not all validator callbacks should
     * transform/clean the value being validated.
     * 
     * @param mixed $value The new value.
     *
     */
    public function setValue($value)
    {
        $this->value = $value;
        $this->hasNewValue = TRUE;
    }
    
    /**
     * Get the newly transformed/cleaned value.
     *
     * @return mixed The new value.
     *
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Returns TRUE if the object contains new tranformed/cleaned value, FALSE otherwise.
     * 
     * @return bool
     * 
     */
    public function hasNewValue()
    {
        return $this->hasNewValue;
    }
    
    /**
     * Add an error message to the result.
     *
     * If the state of the result is valid any calls to this method
     * will be ignored.
     * 
     * @param ValidationErrorMessageInterface $message The message to add.
     * 
     */
    public function addErrorMessage(ValidationErrorMessageInterface $message)
    {
        if ($this->valid)
        {
            return;
        }
        
        $this->errorMessages[] = $message;
    }
    
    /**
     * Add an array of error messages to the result.
     * 
     * Added for brevity in user's code. This method simply loops
     * through the array and call {@see addErrorMessage()}.
     *
     * If the state of the result is valid any calls to this method
     * will be ignored.
     * 
     * @param array $messages Array containing ValidationErrorMessageInterface instances.
     * 
     */
    public function addErrorMessages(array $messages)
    {
        if ($this->valid)
        {
            return;
        }
        
        foreach ($messages as $message)
        {   
            $this->addErrorMessage($message);
        }
    }
    
    /**
     * Get all error messages.
     * 
     * @return array Contains instances of ValidationErrorMessageInterface.
     *
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }
}