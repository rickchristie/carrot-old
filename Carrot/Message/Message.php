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
 * Message
 * 
 * Value object. The default implementation of MessageInterface.
 * Represents generic message/notification from the application to
 * the client. Allows four message types: informational, error,
 * warning, and success.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Message;

use InvalidArgumentException;

class Message implements MessageInterface
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
     * Constructor.
     * 
     * Use MessageInterface constants as types, the message code is
     * optional and defaults to NULL:
     * 
     * <code>
     * $message = new Message(
     *     get_class($this),
     *     'This is an informational message!',
     *     Message::INFORMATIONAL,
     *     '#0001'
     * );
     * </code>
     * 
     * The placeholder syntax is '{:name}'. Example placeholder usage:
     * 
     * <code>
     * $message = new Message(
     *     get_class($this),
     *     "The file '{:fileName}' is corrupted.",
     *     Message::ERROR
     * );
     * $message->setPlaceholders(array(
     *     'fileName' => 'tryout.jpg'
     * ));
     * </code>
     *
     * Get the formatted message:
     *
     * <code>
     * echo $message->get();
     * </code>
     * 
     * According to the above example, the string '{:minLength}' in
     * the message will be replaced by '6', while the string
     * '{:maxLength}' will be replaced by '20'.
     *
     * @param string $issuer Fully qualified class name of the message issuer, without backslash prefix.
     * @param string $message The raw message string.
     * @param string $type The type of the string, value corresponds to MessageInterface interface constants.
     * @param string $code The message's code, NULL if not set.
     *
     */
    public function __construct($issuer, $message, $type = self::INFORMATIONAL, $code = NULL)
    {
        $this->issuer = $issuer;
        $this->message = $message;
        $this->code = $code;
        $this->setType($type);
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
     * The message type should correspond with the constants provided
     * by this interface. This makes it easier to do comparison
     * without the risk of a typo.
     *
     * @param string $type The type of this message.
     *
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * Get the formatted message, with placeholders replaced.
     *
     * @return string Message with placeholders replaced.
     *
     */
    public function get()
    {
        return $this->replacePlaceholders($this->message);
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
}