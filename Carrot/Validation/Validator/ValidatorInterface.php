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
 * Validator Interface
 * 
 * Implement this interface if you want to create your own set of
 * validators. Your validators must not contain any state and
 * should serve as collection of validating methods grouped
 * together in categories (e.g. string validators, etc).
 * 
 * A validator class represents a group of validators and thus may
 * contain more than one validation method. Each validation method
 * must have a validation ID, which consists of a group name and
 * the validator name, separated by a dot. This is enforced by the
 * ValidationChain class when you add your custom validator class
 * to it. The reason for this enforcement is to make validator ID
 * consistent. The structure of the validator ID is as follows
 * (along with the regex used to verify it):
 *
 * <code>
 * groupName.validatorName
 * ([a-zA-Z0-9_]+).([a-zA-Z0-9_]+)
 * </code>
 * 
 * The getCallbacks() method should return an array of callbacks
 * to the validator methods/functions along with their validator
 * ID. Your validator must accept 3 parameters, the ID of the
 * value being validated, the value itself, and validator specific
 * argument.
 * 
 * <code>
 * $callback = function($valueID, $value, $args)
 * {
 *     // Validation logic here
 * };
 * </code>
 * 
 * The validator specific argument can be anything from integer
 * to a complex configuration array. You have to document your
 * validator properly so that users know what type of values your
 * validator callbacks are expecting as a validator specific
 * argument.
 * 
 * Your validator method must return an instance of
 * {@see Carrot\Validation\ValidatorResult}, otherwise
 * ValidationChain will throw an exception.
 * 
 * The ValidationChain class expects getCallbacks() method to
 * return an array containing callbacks to validator methods, with
 * their respective validator IDs as the index. It is suggested
 * that you store the callback functions as methods in your
 * validator class, but this is not enforced.
 * 
 * For more information on how to implement this interface, you
 * can see some example implementations in
 * Carrot\Validation\Validator namespace.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation\Validator;

interface ValidatorInterface
{
    /**
     * Get an array of validator callbacks, along with their ID.
     * 
     * Return an array of callbacks along with their validator ID as
     * their index. It is recommended to use this class's own method
     * to contain the validation logic, although you can also use
     * anonymous functions. Example of the expected array return:
     *
     * <code>
     * return array(
     *     'string.maxLength' => array($this, 'maxLength'),
     *     'string.minLength' => array($this, 'minLength'),
     *     'string.alphanumeric' => array($this, 'alphanumeric'),
     *     ...
     * );
     * </code>
     * 
     * @return array Validator callbacks with their ID as the index.
     * 
     */
    public function getCallbacks();
}