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
 * Validator Result
 *
 * Value object. Represents a result status from a validator
 * callback. The validator callback is to return this object when
 * called. You can pass an implementation of
 * ValidationMessageInterface as a message.
 * 
 * To return a valid result after validation:
 *
 * <code>
 * $result = new ValidatorResult(TRUE);
 * $result->addMessage($warningMessage);
 * return $result;
 * </code>
 * 
 * To return a invalid result after validation:
 * 
 * <code>
 * $result = new ValidatorResult(FALSE);
 * $result->addMessage($message);
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
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation;

class ValidatorResult
{
    /**
     * @var bool If TRUE, then the value passes validation, FALSE otherwise.
     */
    protected $valid;
    
    /**
     * @var array Contains ValidationMessageInterface instances.
     */
    protected $messages = array();
    
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
     * Add a message to the result.
     * 
     * Please note that the messages you add is not required to be an
     * error message. You might want to make it pass the validation
     * but send a warning message, for example.
     * 
     * @param ValidationMessageInterface $message The message to add.
     * 
     */
    public function addMessage(ValidationMessageInterface $message)
    {
        $this->messages[] = $message;
    }
    
    /**
     * Add an array of messages to the result.
     * 
     * Added for brevity in user's code. This method simply loops
     * through the array and call {@see addMessage()}.
     * 
     * @param array $messages Array containing ValidationMessageInterface instances.
     * 
     */
    public function addMessages(array $messages)
    {
        foreach ($messages as $message)
        {   
            $this->addMessage($message);
        }
    }
    
    /**
     * Get all messages.
     * 
     * @return array Contains instances of ValidationMessageInterface.
     *
     */
    public function getMessages()
    {
        return $this->messages;
    }
}