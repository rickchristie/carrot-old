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
 * Represents a message and/or notification from the application
 * to the client. This interface is relied upon by Validation,
 * Form, and Notification classses.
 *
 * There are two categories of messages, parameter messages and
 * general messages. General messages are not tied to any
 * parameter, hence the name 'general' as the message applies for
 * the current request. Parameter messages, in the other hand, is
 * not applicable to the whole request but belong to a particular
 * parameter in the request.
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
 * For more information of the API conventions, see individual
 * methods' documentation or see Carrot's default implementation.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Message;

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
     * Setting the parameter ID will implicitly set the current
     * message type from general message to parameter message.
     * 
     * @see isParameterMessage()
     * @see isGeneralMessage()
     * @param string $parameterID The ID of the parameter where this message belongs to.
     * 
     */
    public function setParameterID($parameterID);
    
    /**
     * Returns the ID of the parameter this message belongs to.
     * 
     * If the message is a parameter message type (implicitly set by
     * setting the parameter ID), this method must return the
     * parameter ID set. Otherwise if this message is a general
     * message (i.e. no attachment to any parameter), this method
     * must return FALSE.
     * 
     * @return string|FALSE Returns the parameter ID, FALSE if no attachment.
     *
     */
    public function getParameterID();
    
    /**
     * Set parameter label to replace parameter IDs.
     * 
     * The parameter ID serves as the input array index and contains
     * the label string as content. Example input array structure:
     * 
     * <code>
     * $parameterLabels = array(
     *     'id' => 'User ID',
     *     'password' => 'Password',
     *     'email' => 'E-mail Address'
     * );
     * </code>
     * 
     * As per the convention, from the above example, the label
     * 'Password' will replace the string '{@password}' and the label
     * 'E-mail Address' will replace the string '{@email}'.
     * 
     * @param array $parameterLabels The labels of parameters, sorted by their ID.
     * 
     */
    public function setParameterLabels(array $parameterLabels);
    
    /**
     * Set placeholder.
     * 
     * You may set placeholders inside the message using the syntax
     * '{:placeholderName}', it will then be replaced by a replacement
     * string sent using this method.
     *
     * The input array index is the placeholder name, while the
     * content of the array is the replacement string, as in:
     * 
     * <code>
     * $placeholders = array(
     *     'minLength' => '6',
     *     'maxLength' => '20'
     * );
     * </code>
     * 
     * According to the above example, the string '{:minLength}' in
     * the message will be replaced by '6', while the string
     * '{:maxLength}' will be replaced by '20'.
     *
     * @param array $placeholders The placeholder name and replacement string in array.
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
    
    /**
     * Get the message, with placeholder and label replaced.
     * 
     * @return string
     *
     */
    public function get();
}