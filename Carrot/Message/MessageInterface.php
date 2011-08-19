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
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Message;

interface MessageInterface
{
    const INFORMATIONAL = 'INFORMATIONAL';
    
    const ERROR = 'ERROR';
    
    const WARNING = 'WARNING';
    
    const SUCCESS = 'SUCCESS';
    
    /**
     * Constructor.
     * 
     * Placeholder syntax: '{:placeholderName}'
     * 
     * @param string $message The message string.
     * 
     */
    public function __construct($issuer, $message, $type = 'INFORMATIONAL', $code = NULL);
    
    public function getIssuer();
    
    public function getMessage();
    
    public function getType();
    
    public function getCode();
    
    public function setMessage($message);
    
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
     * Get the message, with placeholder and label replaced.
     * 
     * @return string
     *
     */
    public function get();
}