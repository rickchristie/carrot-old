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
 * Validation Error Message
 * 
 * The validation message represents an error message issued from
 * the validation layer, which will be rendered by the
 * presentation layer, often a form object.
 * 
 * NOTE: ValidationErrorMessage's type will always be
 * MessageInterface::ERROR. This is hardcoded and follows
 * ValidationErrorMessageInterface specification.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Message;

class ValidationErrorMessage implements ValidationErrorMessageInterface
{
    /**
     * @var string Fully qualified class name of the message issuer, without backslash prefix.
     */
    protected $issuer;
    
    /**
     * @var string The raw message string.
     */
    protected $message;
    
    /**
     * @var string The type of the string, value corresponds to MessageInterface interface constants.
     */
    protected $type;
    
    /**
     * @var string|NULL The message's code, NULL if not set.
     */
    protected $code;
    
    /**
     * @var array The list of placeholder names and their replacement strings.
     */
    protected $placeholders = array();
    
    /**
     * @var string The ID of the value this validation message belongs to.
     */
    protected $valueID;
    
    /**
     * @var array List of value IDs and their label strings.
     */
    protected $labels = array();
    
    /**
     * Constructor.
     * 
     * The placeholder syntax is the same as Carrot\Core\Message:
     *
     * <code>
     * $message = new ValidationErrorMessage(
     *     get_class($this),
     *     "The file '{:fileName}' is corrupted."
     * );
     * $message->setPlaceholders(array(
     *     'fileName' => 'tryout.jpg'
     * ));
     * </code>
     * 
     * The only difference is that this class allows new placeholder
     * syntaxes, such as '{@valueID}' to denote labels:
     * 
     * <code>
     * $message = new ValidationErrorMessage(
     *     get_class($this),
     *     '{@username} must not be less than {:minLength} characters'
     * );
     * $message->setLabels(array(
     *     'username' => 'User Name'
     * ));
     * $message->setPlaceholders(array(
     *     'minLength' => 10
     * ));
     * </code>
     * 
     * Since this is a validation message, it can be attached to a
     * particular validation value:
     *
     * <code>
     * $message->attachTo('username');
     * </code>
     *
     * This will allow whatever object responsible for rendering to
     * properly render the message near the field. You can use
     * '{#label}' placeholder to denote the label of the value which
     * the validation message is attached to:
     *
     * <code>
     * $message = new ValidationErrorMessage(
     *     get_class($this),
     *     '{#label} must not be less than {:minLength} characters'
     * );
     * $message->attachTo('username');
     * $message->setLabels(array(
     *     'username' => 'User Name'
     * ));
     * $message->setPlaceholders(array(
     *     'minLength' => 10
     * ));
     * </code>
     * 
     * @param string $issuer Fully qualified class name of the message issuer, without backslash prefix.
     * @param string $message The raw message string.
     * @param string $type The type of the string, value corresponds to MessageInterface interface constants.
     * @param string $code The message's code, NULL if not set.
     *
     */
    public function __construct($issuer, $message, $code = NULL)
    {
        $this->issuer = $issuer;
        $this->message = $message;
        $this->type = self::ERROR;
        $this->code = $code;
    }
    
    /**
     * Set the message issuer.
     * 
     * @param string $issuer Fully qualified class name of the message issuer, without backslash prefix.
     *
     */
    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;
    }
    
    /**
     * Get the message issuer.
     * 
     * @param string $issuer Fully qualified class name of the message issuer, without backslash prefix.
     *
     */
    public function getIssuer()
    {
        return $this->issuer;
    }
    
    /**
     * Set the message string (with placeholders).
     * 
     * @param string $message The raw message, with placeholders if needed.
     *
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
    
    /**
     * Get the raw message string, with placeholders not replaced.
     *
     * @return string The raw message, with placeholders if needed.
     *
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Set the type of the message.
     * 
     * Since, according to ValidationErrorMessageInterface, the
     * message type should always be ERROR, this method is disabled.
     *
     * @param string $type The type of this message.
     *
     */
    public function setType($type)
    {
        // This is intentionally left blank
    }
    
    /**
     * Get the type of the message.
     * 
     * The message type should correspond with the constants provided
     * by this interface. This makes it easier to do comparison
     * without the risk of a typo.
     * 
     * @return string The type of the message.
     *
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Set the code of the message.
     *
     * @param $code string|NULL The message code, or NULL if it doesn't have it.
     *
     */
    public function setCode($code)
    {
        $this->code = $code;
    }
    
    /**
     * Get the code of the message.
     * 
     * @return string|NULL The message code, or NULL if it doesn't have it.
     * 
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * Attach this message to a specific validation value.
     * 
     * A validation message often is only relevant for a particular
     * validation value. You can use this method to attach the message
     * to a specific value ID.
     * 
     * @param string $valueID The ID of the value this validation message is attached to.
     *
     */
    public function attachTo($valueID)
    {
        $this->valueID = $valueID;
    }
    
    /**
     * Get the value ID this message is attached to.
     * 
     * @return string|NULL Validation value ID, or NULL if not attached to anything.
     * 
     */
    public function getValueID()
    {
        return $this->valueID;
    }
    
    /**
     * Set placeholder name and its replacement.
     * 
     * @param string $name The name of the placeholder to set.
     * @param string $replacement The replacement string.
     *
     */
    public function setPlaceholder($name, $replacement)
    {
        $this->placeholders[$name] = $replacement;
    }
    
    /**
     * Set placeholder names and their replacements in array.
     * 
     * You may set placeholders inside the message using the syntax
     * '{:placeholderName}', it will then be replaced by a replacement
     * string sent using this method.
     *
     * The input array index is the placeholder name, while the
     * content of the array is the replacement string, as in:
     * 
     * <code>
     * $message->setPlaceholders(array(
     *     'minLength' => '6',
     *     'maxLength' => '20'
     * ));
     * </code>
     * 
     * If the placeholder replacement is not set, the placeholder
     * syntax will not be replaced.
     * 
     * NOTE: This method will replace all currently defined
     * placeholder replacements instead of adding it.
     *
     * @param array $placeholders Placeholder names and replacement strings in array.
     *
     */
    public function setPlaceholders(array $placeholders)
    {
        $this->placeholders = $placeholders;
    }
    
    /**
     * Add placeholder names and its replacement strings.
     *
     * Unlike {@see setPlaceholders}, which replaces the currently
     * defined placeholders, this method adds the given placeholder
     * array to the collection. If there are duplicate values, they
     * will be replaced by the new ones.
     * 
     * @param array $placeholders Placeholder names and replacement strings in array.
     * 
     */
    public function addPlaceholders(array $placeholders)
    {
        foreach ($placeholders as $name => $replacement)
        {
            $this->placeholders[$name] = $replacement;
        }
    }
    
    /**
     * Get placeholder names and their replacements in array.
     * 
     * @return array $placeholders The placeholder names and replacements in array.
     * 
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }
    
    /**
     * Set labels for each value IDs.
     * 
     * Used to replace '{@valueID}' placeholders with label strings.
     * The array structure used is similar to the one used in
     * {@see setPlaceholders}:
     *
     * <code>
     * $message->setLabels(array(
     *     'minLength' => '6',
     *     'maxLength' => '20'
     * ));
     * </code>
     * 
     * @param array $labels The value IDs and their labels in array.
     *
     */
    public function setLabels(array $labels)
    {
        $this->labels = $labels;
    }
    
    /**
     * Get the formatted message, with placeholders replaced.
     *
     * @return string Message with placeholders replaced.
     *
     */
    public function get()
    {
        $message = $this->replacePlaceholders($this->message);
        $message = $this->replaceSpecialLabelPlaceholder($message);
        return $this->replaceLabelPlaceholders($message);
    }
    
    /**
     * Replace placeholders with their respective replacement strings.
     * 
     * @see get()
     * @see setPlaceholders()
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
    
    /**
     * Replace '{#label}' placeholder with the correct label placeholder.
     * 
     * The validation message class allows the usage of special label
     * placeholder '{#label}' to refer to the label of the value this
     * message is currently attached to. This method replaces the
     * special placeholder with regular label placeholder, using
     * $valueID class property.
     * 
     * @see attachTo()
     * @param string $message The string that contains the placeholder syntax.
     * 
     */
    protected function replaceSpecialLabelPlaceholder($message)
    {
        if (empty($this->valueID))
        {
            return $message;
        }
        
        $pattern = '/{#label}/';
        $replacement = "{@{$this->valueID}}";
        $message = preg_replace($pattern, $replacement, $message);
        return $message;
    }
    
    /**
     * Replace label placeholders with actual label strings.
     * 
     * @see get()
     * @see setPlaceholders()
     * @param string $message The string that contains the placeholders syntax.
     * @return string The formatted message.
     *
     */
    protected function replaceLabelPlaceholders($message)
    {   
        foreach ($this->labels as $id => $label)
        {
            $pattern = "/{@$id}/";
            $message = preg_replace($pattern, $label, $message);
        }
        
        return $message;
    }
}