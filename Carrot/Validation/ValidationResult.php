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
 * Validation Result
 *
 * Value object. This class represents a result status from a
 * validation method. The validation method is to return this
 * object when called to validate a value. You can pass an
 * implementation of MessageInterface as the representation of the
 * error message.
 * 
 * To return a validation failed result after validation:
 *
 * <code>
 * $result = new ValidationResult;
 * $result->setStatusToInvalid();
 * $result->addMessage($parameterErrorMessage);
 * return $result;
 * </code>
 *
 * To return a validation successful result after validation:
 *
 * <code>
 * $result = new ValidationResult;
 * $result->setStatusToValid();
 * $result->addMessage($warningMessage);
 * return $result;
 * </code>
 *
 * If your validator method transforms the value of the parameter,
 * e.g. normalizing a date string, don't forget to put the new
 * value into the object:
 * 
 * <code>
 * $result->setNewValue('date', $formattedDate);
 * </code> 
 *
 * The ValidationChain object will check for new values after it
 * receives a ValidationResult object and act accordingly.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation;

use Carrot\Message\MessageInterface;

class ValidationResult
{
    /**
     * @var bool The status of the result, TRUE means valid, FALSE means invalid.
     */
    protected $status;
    
    /**
     * @var array An array containing MessageInterface instances.
     */
    protected $messages = array();
    
    /**
     * @var array Contains new values, transformed/cleaned by the validator.
     */
    protected $newValues = array();
    
    /**
     * Sets the status to invalid.
     * 
     * You will have to call this method explicitly to set the status
     * of the result, otherwise the status will not be set.
     * 
     */
    public function setStatusToInvalid()
    {
        $this->status = FALSE;
    }
    
    /**
     * Sets the status to valid (passes validation).
     * 
     * You will have to call this method explicitly to set the status
     * of the result, otherwise the status will not be set.
     * 
     */
    public function setStatusToValid()
    {
        $this->status = TRUE;
    }
    
    /**
     * Returns TRUE if the result is valid.
     * 
     * @return bool TRUE if valid result, FALSE otherwise.
     * 
     */
    public function isValid()
    {
        return $this->status;
    }
    
    /**
     * Records a value change.
     * 
     * Useful when you wanted to do a cleaning, normalization, or
     * conversion of the input data. Use this method to tell
     * ValidationChain to update the parameter values it stores.
     *
     * <code>
     * $result->setNewValue('date', $formattedDate);
     * </code>
     * 
     * @param string $parameterID The ID of the parameter whoose value we wanted to change.
     * @param string $newValue The new value to change.
     * 
     */
    public function setNewValue($parameterID, $newValue)
    {
        $this->newValues[$parameterID] = $newValue;
    }
    
    /**
     * Gets the new values.
     * 
     * Example of returned array:
     * 
     * <code>
     * $newValues = array(
     *     'date' => '2012-06-19',
     *     'text' => 'filtered input',
     * );
     * </code>
     * 
     * @return array The changed values with their parameter ID as the indexes.
     *
     */
    public function getNewValues()
    {
        return $this->newValues;
    }
    
    /**
     * Returns TRUE if the result instance has instruction to change the value.
     * 
     * @return bool TRUE if object contains newly transformed values, FALSE otherwise.
     *
     */
    public function hasNewValues()
    {
        return (empty($this->newValues));
    }
    
    /**
     * Add a message to the result.
     * 
     * Please note that the messages you add is not required to be an
     * error message. You might want to make it pass the validation
     * but send a warning message, for example.
     * 
     * @param MessageInterface $message The message to add.
     * 
     */
    public function addMessage(MessageInterface $message)
    {
        $this->messages[] = $message;
    }
    
    /**
     * Add an array of messages to the result.
     * 
     * Added for brevity in user's code. This method simply loops
     * through the array and call {@see addMessage}.
     * 
     * @param array $messages Array containing MessageInterface instances.
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
     * Get the messages in an array.
     * 
     * @return array Contains instances of MessageInterface.
     *
     */
    public function getMessages()
    {
        return $this->messages;
    }
}