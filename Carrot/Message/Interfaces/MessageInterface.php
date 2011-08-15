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
 * Message Interface
 *
 * Represents the contract between a message and the ProcessResult
 * object as the container of messages. There are two categories
 * of messages, parameter messages and general messages. General
 * messages are not tied to any parameter, hence the name
 * 'general' as the message applies for the current request.
 * Parameter messages, in the other hand, is not applicable to
 * the whole request but belong to a particular parameter in the
 * request.
 * 
 * General messages are usually process successful, warning, or
 * informational messages, although this is not always the case.
 * Example of general messages:
 *
 * <code>
 * The process is completed successfully!
 * Warning: Your account will expire in 19 Jan, 2012.
 * Happy 23rd birthday from the team!
 * You have 5 new messages.
 * The form request was malformed, please try again.
 * </code>
 *
 * Parameter messages are usually used when the message tells that
 * something is wrong on the parameter sent. This is very useful
 * in conveying errors after parameter validation. Example of
 * parameter messages:
 *
 * <code>
 * Username must not be more than 7 characters length.
 * Email address is not valid.
 * Two password fields must match each other.
 * </code>
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Process\Interfaces;

interface MessageInterface
{
    /**
     * Constructor.
     * 
     * The message must follow the convention of parameter label
     * syntax '{@parameterID}' and placeholder syntax
     * '{:placeholder}'. Example of message string utilizing both:
     *
     * <code>
     * {@username} must not be more than {:maxLength} characters in length.
     * </code>
     * 
     * @param string $message The message string.
     * 
     */
    public function __construct($message);
    
    /**
     * Set the ID of the parameter where this message belongs to.
     * 
     * Setting the parameter ID must implicitly set the current
     * message type from general message to parameter message.
     * 
     * @see isParameterMessage()
     * @see isGeneralMessage()
     * @param string $parameterID The ID of the parameter where this message belongs to.
     * 
     */
    public function setParameterID($parameterID);
    
    /**
     * Set parameter label to replace parameter IDs.
     * 
    // ---------------------------------------------------------------
     * As convention, send the array using the following structure:
     *
     * <code>
     * $parameterLabels = array(
     *     'id' => 'User ID',
     *     'password' => 'Password',
     *     'email' => 'E-mail Address'
     * );
     * </code>
     * 
     * @param array $parameterLabels The labels of parameters, sorted by their ID.
     * 
     */
    public function setParameterLabels(array $parameterLabels);
    
    /**
     * Set placeholder.
     * 
    // ---------------------------------------------------------------
     * For easier 
     * 
     * 
     */
    public function setPlaceholders(array $placeholders);
    
    /**
     * Checks if the message is general or not.
     * 
     * General messages applies to the whole request and are not tied
     * to any particular parameter.
     * 
     * @return bool TRUE if general message, FALSE otherwise.
     * 
     */
    public function isGeneralMessage();
    
    /**
     * Checks if the message belongs to a specific parameter.
     * 
     * Parameter messages applies to only to a specific parameter in
     * the request.
     * 
     * @return bool TRUE if a parameter message, FALSE otherwise.
     * 
     */
    public function isParameterMessage();
}