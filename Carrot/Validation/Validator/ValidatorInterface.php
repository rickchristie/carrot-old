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
 * ID. Your validator must accept 3 parameters: the parameter ID
 * currently active in the chain, an array containing all the
 * parameters, and validator specific arguments.
 *
 * Example method/function signature:
 *
 * <code>
 * function validator($activeValueID, array $parameters, $args)
 * {
 *     // Do validation
 * }
 * </code>
 * 
 * $activeValueID is the ID of the parameter being validated for
 * the current validation chain. This may be NULL, as its value
 * depends on how the user calls ValidationChain::start(), so you
 * have to check it first before using it.
 * 
 * $parameters simply contain the list of parameters to validate
 * in an associative array. Depending on what the validator is
 * trying to do, it may disregard $activeValueID entirely and work
 * directly with the values and indexes of $parameters. If the
 * variables you need doesn't exist in $parameters, throw a
 * {@see Validator\Exception\MissingParameterException}.
 * 
 * $args is the validator specific argument. It can be anything
 * from string to a configuration array. You have to document
 * your validator properly so that users know what type of values
 * you are expecting in $args, and what does it represents. For
 * example, you send the maximum length integer as the validator
 * specific argument when running string.maxLength, and you pass
 * an array containing IDs of the parameters whose values must
 * match each other when you're running comparison.matches.
 * 
 * Your validator method must return an instance of
 * {@see ValidationResult}, otherwise ValidationChain will throw
 * an exception.
 * 
 * The ValidationChain class expects getCallbacks() method to
 * return an array containing callbacks to validator methods, with
 * their respective validator IDs as the index. It is suggested
 * that you store the callback functions as methods in your
 * validator class, but this is not enforced.
 * 
 * For more information on how to implement this interface, you
 * can see some example implementations on Validators namespace.
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