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
 * Validator Message Interface
 *
 * The validation message is represents a message issued from the
 * validation layer, be it from regular validators or other
 * validation layer objects.
 * 
 * This interface defines the contract between the validation
 * message class with the objects responsible to render it.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation;

use Carrot\Message\MessageInterface;

interface ValidationMessageInterface extends MessageInterface
{
    /**
     * Attach the message to a specific value.
     *
     * @param string $valueID The ID of the value this validation message is attached to.
     *
     */
    public function attachTo($fieldID);
    
    /**
     * Get ID of the value this message is attached to.
     *
     * Should return NULL if not set yet.
     *
     * @return string|NULL Validation value ID, or NULL if not attached to anything.
     *
     */
    public function getValueID();
    
    /**
     * Set labels for value IDs.
     * 
     * The array structure is as follows:
     *
     * <code>
     * $labels = array(
     *     'valueID' => 'Label',
     *     'id' => 'User ID',
     *     'pwd' => 'Password'
     * );
     * </code>
     *
     * @param array $labels The value IDs and their labels in array.
     * 
     */
    public function setLabels(array $labels);
}