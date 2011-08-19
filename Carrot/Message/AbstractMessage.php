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
 * Abstract Message
 * 
 * Represents a message and/or notification from the application
 * to the client. This interface is relied upon by Validation,
 * Form, and Notification classses.
 *
 * This is an abstract class and must be extended to provide
 * context to the message. Default contexts provided by Carrot
 * is ErrorMessage, WarningMessage, SuccessMessage, and
 * InformationalMessage. These default contexts are used widely
 * by Validation and Form libraries. You may create your own
 * contexts (say, BirthdayMessage) by extending this abstract
 * class. Your view classes can then render them appropriately
 * based on their contexts.
 * 
 * There are two categories of messages, parameter messages and
 * general messages. General messages are not tied to any
 * parameter, hence the name 'general' as the message applies for
 * the current request. Parameter messages, in the other hand, is
 * not applicable to the whole request but belong to a particular
 * parameter in the request.
 * 
 * To create general messages:
 * 
 * <code>
 * $message = new WarningMessage('System Alert! Enemies at bay!');
 * </code>
 * 
 * To create parameter messages, first create the message, then
 * assign it to a parameter ID using {@see setParameterID()}:
 * 
 * <code>
 * $message = new ErrorMessage('This field must not be empty!');
 * $message->setParameterID('username');
 * </code>
 *
 * You can use parameter ID syntax to refer to a parameter's
 * labels. They will be replaced as long as the parameter labels
 * are set correctly using {@see setParameterLabels()}:
 *
 * <code>
 * $message = new ErrorMessage('{@password} must match {@passwordConfirm}');
 * $message->setParameterID('password');
 * $message->setParameterLabels(array(
 *     'password' => 'Password',
 *     'passwordConfirm' => 'Password Confirmation'
 * );
 * </code>
 * 
 * You can also use placeholder syntax to refer to variable
 * string that you wanted to display. For example:
 *
 * <code>
 * $message = new ErrorMessage('{@username} must be more than {:minLength} characters length.');
 * $message->setParameterID('username');
 * $message->setParameterLabels(array(
 *     'username' => 'User Name'
 * ));
 * $message->setPlaceholders(array(
 *     'minLength' => '10'
 * ));
 * </code>
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Message;

abstract class AbstractMessage implements MessageInterface
{
    /**
     * @var string The message string, without formatting.
     */
    protected $rawMessage;
    
    /**
     * @var string The ID of the parameter where this message belongs to.
     */
    protected $parameterID;
    
    /**
     * @var array The list of parameter names and their labels.
     */
    protected $parameterLabels = array();
    
    /**
     * @var array The list of placeholder names and their replacements.
     */
    protected $placeholders = array();
    
    /**
     * Constructor.
     * 
     * Pass the message string during construction:
     * 
     * <code>
     * $message = new Message('This is an informational message.');
     * </code> 
     * 
     * You can pass parameter ID syntax as a reference to a parameter
     * label when constructing. They will be replaced as long as the
     * parameter labels are set correctly using
     * {@see setParameterLabels()}:
     *
     * <code>
     * $message = new ErrorMessage('{@password} must match {@passwordConfirm}');
     * </code>
     *
     * You can pass placeholder syntax to refer to a variable string
     * you wanted to display. They will be replaced as long as the
     * placeholders are set correctly using {@see setPlaceholders()}:
     *
     * <code>
     * $message = new ErrorMessage('Today is {:placeholder}, good morning!');
     * </code>
     * 
     * @param string $message The message to send.
     * 
     */
    public function __construct($rawMessage)
    {
        $this->rawMessage = $rawMessage;
    }
    
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
    public function setParameterID($parameterID)
    {
        $this->parameterID = $parameterID;
    }
    
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
    public function getParameterID()
    {
        if (empty($this->parameterID))
        {
            return FALSE;
        }
        
        return $this->parameterID;
    }
    
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
    public function setParameterLabels(array $parameterLabels)
    {
        $this->parameterLabels = $parameterLabels;
    }
    
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
    public function setPlaceholders(array $placeholders)
    {
        $this->placeholders = $placeholders;
    }
    
    /**
     * Checks if the message is general or not.
     * 
     * General messages applies to the whole request and are not tied
     * to any particular parameter.
     * 
     * @return bool TRUE if general message, FALSE otherwise.
     * 
     */
    public function isGeneralMessage()
    {
        return (empty($this->parameterID));
    }
    
    /**
     * Checks if the message belongs to a specific parameter.
     * 
     * Parameter messages applies to only to a specific parameter in
     * the request.
     * 
     * @return bool TRUE if a parameter message, FALSE otherwise.
     * 
     */
    public function isParameterMessage()
    {
        return (!empty($this->parameterID));
    }
    
    /**
     * Get the message, with placeholder and label replaced.
     * 
     * If the parameter label or the placeholder replacements are not
     * available, they will be left untouched.
     * 
     * @return string The complete message.
     *
     */
    public function get()
    {
        $message = $this->replaceParameterIDs($this->rawMessage);
        $message = $this->replacePlaceholders($message);
        return $message;
    }
    
    /**
     * Replace parameter ID syntaxes with their respective labels.
     * 
     * If the parameter label is not set, the parameter ID syntax
     * will not be replaced.
     * 
     * @param string $message The string that contains the parameter ID syntax.
     * @return string The formatted message.
     * 
     */
    protected function replaceParameterIDs($message)
    {   
        foreach ($this->parameterLabels as $id => $label)
        {
            $pattern = "/{@$id}/";
            $message = preg_replace($pattern, $label, $message);
        }
        
        return $message;
    }
    
    /**
     * Replace placeholder names with their respective replacement strings.
     * 
     * If the placeholder replacement is not set, the placeholder
     * syntax will not be replaced.
     * 
     * @param string $message The string that contains the placeholders syntax.
     * @return string The formatted message.
     *
     */
    protected function replacePlaceholders($message)
    {
        foreach ($this->placeholders as $name => $replacement)
        {
            $pattern = "/{:$name}/";
            $message = preg_replace($pattern, $replacement, $message);
        }
        
        return $message;
    }
}