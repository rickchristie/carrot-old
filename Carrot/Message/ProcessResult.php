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
 * Process Result
 *
// ---------------------------------------------------------------
 * This class represents a generic result status from a process
 * done by a method. It is also a container for MessageInteface
 * instances.
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Process;

use Carrot\Process\Interfaces\MessageInterface;

class ProcessResult
{
    /**
     * @var bool The status of the result, if TRUE then successful, FALSE otherwise.
     */
    protected $status;
    
    /**
     * @var array List of MessageInterface instance not belonging to any parameter.
     */
    protected $generalMessages;
    
    /**
     * @var array List of MessageInterface instance belonging to a parameter.
     */
    protected $parameterMessages;
    
    /**
     * Set the status to failure.
     * 
     * You must explicitly set the status with either with this method
     * or {@see setStatusToSuccessful()} after object construction.
     * 
     */
    public function setStatusToFailure()
    {
        $this->status = FALSE;
    }
    
    /**
     * Process successful
     * 
     * You must explicitly set the status with either with this method
     * or {@see setStatusToFailure()} after object construction.
     * 
     */
    public function setStatusToSuccessful()
    {
        $this->status = TRUE;
    }
    
    /**
     * Returns TRUE if the process status is successful, FALSE otherwise.
     * 
     * @return bool
     *
     */
    public function isSuccessful()
    {
        return $this->status;
    }
    
    /**
     * Returns TRUE if the process status is failure, FALSE otherwise.
     *
     * @return bool
     *
     */
    public function isFailure()
    {
        return ($this->status === FALSE);
    }
    
    /**
     * Add an array of messages.
     * 
    // ---------------------------------------------------------------
     * Accepts 
     *
     * @param array $messages Must contain MessageInterface implementations.
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
     * Add message.
     * 
     * 
     * 
     * @param MessageInterface $message 
     * 
     */
    public function addMessage(MessageInterface $message)
    {
        if ($this->isMessageValid($message) == FALSE)
        {
            throw new InvalidArgumentException("ProcessResult error in adding messages. The message given must be an implementation of Carrot\Process\Interfaces\MessageInterface.");
        }
        
        if ($message->isGeneralMessage())
        {
            $this->generalMessages[] = $message;
        }
        else
        {
            $this->parameterMessages[] = $message;
        }
    }
    
    /**
     * Returns an array of general messages.
     * 
     * 
     * 
     * @return array Contains MessageInterface implementations.
     * 
     */
    public function getGeneralMessages()
    {
        return $this->generalMessages;
    }
    
    /**
     * Returns an array of parameter messages.
     * 
     * 
     * 
     * @return array Contains MessageInterface implementations.
     *
     */
    public function getParameterMessages()
    {
        return $this->parameterMessages;
    }
    
    /**
     * Checks if the message is valid.
     * 
     * 
     * 
     * @param MessageInterface $message The message instance to validate.
     * @return bool TRUE if the message is valid, FALSE otherwise.
     *
     */
    protected function isMessageValid($message)
    {
        return (is_object($message) AND $message instanceof MessageInterface);
    }
}