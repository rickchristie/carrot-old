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
 * Message Interface
 * 
 * Represents a message and/or notification from the application
 * to the client. Allows the usage of placeholders for easier
 * internationalization. This interface recognizes four types of
 * messages: informational, error, warning, and success.
 * 
 * This interface serves as the basic interface for messages. You
 * are encouraged to extend this interface and create special
 * messages when you need it.
 *
 * For more information, see the default implementation at
 * {@see Message}.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Message;

interface MessageInterface
{
    /**
     * Used to denote a message as informational.
     */
    const INFORMATIONAL = 'INFORMATIONAL';
    
    /**
     * Used to denote a message as an error.
     */
    const ERROR = 'ERROR';
    
    /**
     * Used to denote a message as a warning.
     */
    const WARNING = 'WARNING';
    
    /**
     * Used to denote a message as a sucess message.
     */
    const SUCCESS = 'SUCCESS';
    
    /**
     * Set the issuer of the message.
     * 
     * The issuer of the message should be the class that created the
     * message, along with its namespace. You can use PHP's
     * get_class() function to quickly create an issuer string.
     * 
     * NOTE: The 'issuer' property is mandatory, it should always have
     * a valid value.
     * 
     * @param string $issuer Fully qualified class name of the issuer, without backslash prefix.
     *
     */
    public function setIssuer($issuer);
    
    /**
     * Get the issuer of the message.
     *
     * The issuer of the message should be the class that created the
     * message, along with its namespace. You can use PHP's
     * get_class() function to quickly create an issuer string.
     *
     * NOTE: The 'issuer' property is mandatory, it should always have
     * a valid value.
     * 
     * @return string Fully qualified class name of the issuer, without backslash prefix.
     * 
     */
    public function getIssuer();
    
    /**
     * Set the message string.
     * 
     * The message should be able to use placeholders. It is
     * recommended that your placeholder syntax follows the default
     * placeholder syntax in {@see Message}, but this is not required.
     * 
     * NOTE: The 'message' property is mandatory, it should always
     * have a valid value.
     * 
     * @param string $message The raw message, with placeholder syntaxes.
     *
     */
    public function setMessage($message);
    
    /**
     * Get the unformatted message, with placeholders not replaced.
     * 
     * NOTE: The 'message' property is mandatory, it should always
     * have a valid value.
     * 
     * @return string The unformatted message, with placeholder not replaced.
     *
     */
    public function getMessage();
    
    /**
     * Set the type of the message.
     * 
     * The value of the type should correspond with the constants
     * provided by this interface. However, you are free to create
     * your own message types by extending this interface and creating
     * new constants.
     * 
     * NOTE: The 'type' property is mandatory, it should always have
     * a valid value.
     * 
     * @param string $type The type of the message.
     *
     */
    public function setType($type);
    
    /**
     * Get the type of the message.
     * 
     * The message type should correspond with the constants provided
     * by this interface. This makes it easier to do comparison
     * without the risk of a typo.
     * 
     * NOTE: The 'type' property is mandatory, it should always have
     * a valid value.
     * 
     * @return string The type of the message.
     * 
     */
    public function getType();
    
    /**
     * Set the code of the message.
     * 
     * Using codes to mark messages might be useful in some scenarios,
     * however it is not mandatory.
     * 
     * @param $code string|NULL The message code, or NULL if it doesn't have it.
     * 
     */
    public function setCode($code);
    
    /**
     * Get the code of the message.
     * 
     * Using codes to mark messages might be useful in some scenarios,
     * however it is not mandatory.
     * 
     * @return string The code of the message.
     * 
     */
    public function getCode();
    
    /**
     * Set placeholder name and its replacement.
     * 
     * @param string $name The name of the placeholder to set.
     * @param string $replacement The replacement string.
     *
     */
    public function setPlaceholder($name, $replacement);
    
    /**
     * Set placeholder names and their replacements in array.
     * 
     * The placeholder name serves as the array index while the
     * replacement string serves as the array content. Please
     * {@see Message} for a default implementation.
     * 
     * NOTE: This method will replace all currently defined
     * placeholder replacements instead of adding it.
     *
     * @param array $placeholders Placeholder names and replacement strings in array.
     * 
     */
    public function setPlaceholders(array $placeholders);
    
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
    public function addPlaceholders(array $placeholders);
    
    /**
     * Get placeholder names and their replacements in array.
     * 
     * @return array $placeholders The placeholder names and replacements in array.
     * 
     */
    public function getPlaceholders();
    
    /**
     * Get the formatted message, with placeholders replaced.
     * 
     * The message string returned from this method should have its
     * placeholders properly replaced. It should not be escaped, as
     * this is not a responsibility of MessageInterface.
     * 
     * @return string Formatted message, with placeholder replaced.
     *
     */
    public function get();
}