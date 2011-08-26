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
 * Validation Chain
 * 
 * Contains validator callbacks and represents a chain of
 * validation against a specific value. To be used in validation
 * layer for small, generic bits of validation.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation;

use RuntimeException,
    InvalidArgumentException,
    Carrot\Validation\Validator\ComparisonValidator,
    Carrot\Validation\Validator\DateValidator,
    Carrot\Validation\Validator\EmailValidator,
    Carrot\Validation\Validator\NotEmptyValidator,
    Carrot\Validation\Validator\NumberValidator,
    Carrot\Validation\Validator\StringValidator,
    Carrot\Validation\Validator\TypeValidator,
    Carrot\Validation\Validator\ValidatorInterface;

class ValidationChain
{   
    /**
     * @var mixed The value to be validated.
     */
    protected $values;
    
    /**
     * @var array List of validator callbacks and their validator ID.
     */
    protected $callbacks = array();
    
    /**
     * @var string The ID of the value currently validated in this chain.
     */
    protected $activeValueID;
    
    /**
     * @var bool TRUE initially, turns into FALSE if failed at least one validation.
     */
    protected $valid = TRUE;
    
    /**
     * @var array Contains ValidationErrorMessageInterface instances.
     */
    protected $errorMessages = array();
    
    /**
     * @var bool If TRUE, then the validation chain is optional, {@see startOptional}.
     */
    protected $optional = FALSE;
    
    /**
     * @var bool If TRUE, all calls to {@see validate()} will be ignored.
     */
    protected $ignore = TRUE;
    
    /**
     * Constructor.
     * 
     * This class will first register all default validators before
     * loading your custom validators. This allows your custom
     * validators to override the default validators.
     *
     * <code>
     * $chain = new ValidatorChain(array(
     *     $businessRuleValidator,
     *     $decimalValidator,
     *     $ISBNValidator
     * ));
     * </code>
     * 
     * You can then start the chain:
     *
     * <code>
     * $chain->start('username', $username)
     *       ->validate('string.maxLength', 10)
     *       ->validate('string.minLength', 5)
     *       ->validate('string.alphanumeric');
     * </code>
     * 
     * @param array $customValidators List of validators to be added.
     * 
     */
    public function __construct(array $customValidators = array())
    {
        $this->registerDefaultValidators();
        
        foreach ($customValidators as $validator)
        {
            $this->registerValidator($validator);
        }
    }
    
    /**
     * Add a custom validator class.
     * 
     * Inject your custom validator class with this method:
     *
     * <code>
     * $chain->registerValidator($businessRuleValidator);
     * $chain->registerValidator($decimalValidator);
     * $chain->registerValidator($ISBNValidator);
     * </code>
     * 
     * Throws RuntimeException if ValidatorInterface::getCallbacks()
     * does not return an array.
     * 
     * @throws RuntimeException
     * @param ValidatorInterface $validator Validator instance to add.
     * 
     */
    public function registerValidator(ValidatorInterface $validator)
    {
        $callbacks = $validator->getCallbacks();
        
        if (!is_array($callbacks))
        {
            $class = get_class($validator);
            throw new InvalidArgumentException("ValidationChain error in adding validator. The validator class '{$class}' does not return an array when getCallbacks() method is called.");
        }
        
        foreach ($callbacks as $validatorID => $callback)
        {
            $this->validatorIDMustBeValid($validatorID);
            
            if (!is_callable($callback))
            {
                throw new InvalidArgumentException("ValidationChain error in adding validator callback. The callback given for validator ID '{$validatorID}' is not callable.");
            }
            
            $this->callbacks[$validatorID] = $callback;
        }
    }
    
    /**
     * Add a validator callback.
     * 
     * Generally, custom validators are coded into a specific
     * Validator class, which is then injected to the ValidatorChain.
     * This makes reuse of the custom validation logic easier. This
     * method, however, allows you to skip the creation of custom
     * validation class and directly register a validator callback.
     * While this may harm reuse, it certainly helps encapsulation of
     * validation logic when you need it.
     *
     * You can either pass an anonymous function:
     * 
     * <code>
     * $chain->registerValidatorCallback('business.customLogic',
     *     function($activeValueID, array $values, $args)
     *     {
     *         // Custom validator logic
     *     }
     * );
     * </code>
     *
     * Or simple array callback:
     *
     * <code>
     * $chain->registerValidatorCallback('business.customLogic',
     *     array($this, 'methodName')
     * );
     * </code>
     * 
     * @param string $validatorID The ID of the validator.
     * @param callback $callback The validator callback.
     * 
     */
    public function registerValidatorCallback($validatorID, $callback)
    {
        $this->validatorIDMustBeValid($validatorID);
        
        if (!is_callable($callback))
        {
            throw new InvalidArgumentException("ValidationChain error in adding validator callback. The callback given is not callable.");
        }
        
        $this->callbacks[$validatorID] = $callback;
    }
    
    /**
     * Starts/restarts the validation chain.
     * 
     * You must run this method before you start validating.
     *
     * <code>
     * $chain->start('username')
     *       ->validate('string.maxLength', 10)
     *       ->validate('string.minLength', 5)
     *       ->validate('string.alphanumeric');
     * </code>
     * 
     * You don't need to call an explicit stop method since the start
     * method is already explicit.
     * 
     * @param string $valueID The ID of the value to be validated.
     * @param string $value The contents of the value.
     * @return ValidationChain This object itself.
     *
     */
    public function start($valueID)
    {
        if ($valueID != NULL AND !array_key_exists($valueID, $this->values))
        {
            throw new InvalidArgumentException("ValidationChain error when trying to start chain. The value ID '{$valueID}' is not set.");
        }
        
        $this->activeValueID = $valueID;
        $this->ignore = FALSE;
        $this->optional = FALSE;
        return $this;
    }
    
    /**
     * Starts/restarts the chain in optional mode.
     * 
     * When a chain is in optional mode, if the first validation
     * fails, it will not be noted as a validation failure, and the
     * rest of the chain will be skipped. This is useful in values
     * that you wanted to validate but doesn't want to if it doesn't
     * exist.
     * 
     * @see start()
     * @param string $valueID The ID of the value to be validated.
     * @param string $value The contents of the value.
     * @return ValidationChain This object itself.
     *
     */
    public function startOptional($valueID)
    {
        if ($valueID != NULL AND !array_key_exists($valueID, $this->values))
        {
            throw new InvalidArgumentException("ValidationChain error when trying to start chain. The value ID '{$valueID}' is not set.");
        }
        
        $this->activeValueID = $valueID;
        $this->ignore = FALSE;
        $this->optional = TRUE;
        return $this;
    }
    
    /**
     * Reset the state of the validation chain.
     *
     * When this method is called, the state of this class is
     * reset back as if the it was just constructed. This means class
     * properties, such as $valid and $messages will be returned to
     * default values. Make sure you have taken the messages and
     * checked for validity before reseting the chain.
     *
     */
    public function reset()
    {
        $this->values = array();
        $this->ignore = TRUE;
        $this->valid = TRUE;
        $this->errorMessages = array();
        $this->optional = FALSE;
    }
    
    /**
     * Run a validator.
     * 
     * Runs the validator callback and process its result object. Will
     * add messages and updates value with new, transformed value
     * regardless of whether the result is valid or invalid (except if
     * the chain is optional).
     * 
     * To use, first start the chain {@see start()}:
     *
     * <code>
     * $chain->start('username')
     *       ->validate('string.maxLength', 10)
     *       ->validate('string.minLength', 5)
     *       ->validate('string.alphanumeric');
     * 
     * if ($chain->isValid())
     * {
     *     // Do stuffs
     * }
     * </code>
     * 
     * What you need to send as the second argument depends on
     * the validator method. The second argument for
     * 'string.maxLength', for example, must be an integer and
     * represents the maximum string length for the value being
     * validated. Read the docs of each validator so you know what you
     * need to send. Validators should throw relevant exceptions if
     * the arguments provided is invalid.
     *
     * Normally, validation will still run even after an invalid
     * result is returned. This behavior can be overridden by passing
     * TRUE for the third argument, $breakChainOnFailure:
     *
     * <code>
     * // Stop chain if an invalid result is returned
     * $chain->start('username')
     *       ->validate('notEmpty.default', NULL, TRUE)
     *       ->validate('string.MinLength', 5, TRUE)
     *       ->validate('string.MaxLength', 10, TRUE);
     * </code>
     * 
     * Throws InvalidArgumentException if the validator ID provided
     * doesn't exist in this class's callback list.
     * 
     * Throws RuntimeException if this method is called but the chain
     * has not been started yet.
     * 
     * @see start()
     * @see startOptional()
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @param string $validatorID The ID of the validator callback to be used.
     * @param mixed $args Arguments to be passed to the validator method.
     * @param bool $breakChainOnFailure If TRUE, break chain if result is invalid. Defaults to FALSE.
     * @return ValidationChain This object itself.
     * 
     */
    public function validate($validatorID, $args = NULL, $breakChainOnFailure = FALSE)
    {   
        if ($this->ignore)
        {
            return;
        }
        
        if (!array_key_exists($validatorID, $this->callbacks))
        {
            throw new InvalidArgumentException("ValidationChain error when trying to validate. Validator callback with the ID '{$validatorID}' does not exist.");
        }
        
        $result = $this->runValidatorCallback($validatorID, $args);
        
        if (!is_object($result) OR !($result instanceof ValidatorResult))
        {
            throw new RuntimeException("ValidationChain error when trying to validate. Validator callback with the ID '{$validatorID}' does not return an instance of Carrot\Validation\ValidatorResult.");
        }
        
        // If this is an optional chain and the result is
        // valid, the chain is no longer optional and must
        // be completed.
        if ($result->isValid() AND $this->optional)
        {
            $this->optional = FALSE;
        }
        
        // If this is an optional chain and the result is
        // invalid, we must ignore the rest of the chain
        // and we must not add the messages or change the
        // value of $valid class property.
        if (!$result->isValid() AND $this->optional)
        {
            $this->ignore = TRUE;
            return $this;
        }
        
        if (!$result->isValid())
        {
            $this->valid = FALSE;
            
            if ($breakChainOnFailure)
            {
                $this->ignore = TRUE;
            }
        }
        
        $this->updateValue($result);
        $this->addErrorMessages($result);
        return $this;
    }
    
    /**
     * Returns TRUE if passes all validation chains, FALSE otherwise.
     * 
     * @return bool
     *
     */
    public function isValid()
    {
        return $this->valid;
    }
    
    /**
     * Get ValidationErrorMessageInterface instances from validators.
     * 
     * @return array Contains instances of ValidationErrorMessageInterface.
     * 
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }
    
    /**
     * Sets values to validate.
     * 
     * Before you can start a validation chain, you have to set the
     * values to validate first. Each value must have an ID, which
     * will later be used by the returned messages to attach itself
     * to. The view layer can later read the value ID and use it to
     * set labels on the messages.
     *
     * <code>
     * $chain->setValues(array(
     *     'firstName' => $firstName,
     *     'lastName' => $lastName,
     *     'email' => $email,
     * ));
     * </code>
     * 
     * @param array $values List of values to be validated, sorted by their value ID.
     * 
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }
    
    /**
     * Get the newly transformed/cleaned values.
     * 
     * Some validators may perform transformation/cleaning to the
     * value. This method allows the chain caller to get the newly
     * transformed values.
     * 
     * @return mixed The new values in associative array.
     *
     */
    public function getValues()
    {
        return $this->values;
    }
    
    /**
     * Register default validators.
     * 
     * Validation library comes with a set of default validators,
     * which are registered using this method at construction.
     *
     * @see __construct()
     * 
     */
    protected function registerDefaultValidators()
    {
        $this->registerValidator(new NotEmptyValidator);
        $this->registerValidator(new NumberValidator);
        $this->registerValidator(new StringValidator);
        $this->registerValidator(new ComparisonValidator);
        $this->registerValidator(new TypeValidator);
    }
    
    /**
     * Throws exception if the validator ID is not valid.
     * 
     * A validator ID is valid if it has a validator type (basic or
     * complex), a group name, and a validator name, separated by dot.
     * It must match this regex:
     *
     * <code>
     * ^([a-zA-Z0-9_]+).([a-zA-Z0-9_]+)$
     * </code>
     * 
     * This method also makes sure that there are no duplicate
     * validator ID.
     *
     * Throws InvalidArgumentException if the validator ID does not
     * match the above mentioned regex.
     *
     * Throws InvalidArgumentException if the validator ID is already
     * defined in this class.
     * 
     * @throws InvalidArgumentException
     * @param string $validator ID The ID to check.
     * 
     */
    protected function validatorIDMustBeValid($validatorID)
    {
        $regexMatches = preg_match('/^([a-zA-Z0-9_]+).([a-zA-Z0-9_]+)$/D', $validatorID);
        
        if ($regexMatches <= 0)
        {
            throw new InvalidArgumentException("ValidationChain error in adding validator callback. The validator ID '{$validatorID}' is not valid. Validator ID must have a group name and a validator name, separated by dot.");
        }
    }
    
    /**
     * Runs validator callback.
     * 
     * This method first checks the type of the callback. If it's an
     * array callback, it assumes that it is an object and a method
     * name (don't ask why it doesn't assume a class name and a
     * static method). If it's not an array it assumes the callback is
     * an anonymous function.
     * 
     * @param string $validatorID The validator ID to be run.
     * @param mixed $args Validator specific argument to be passed to the callback.
     * @return mixed The return value of the validator callback.
     *
     */
    protected function runValidatorCallback($validatorID, $args)
    {
        $callback = $this->callbacks[$validatorID];
        
        if (is_array($callback))
        {
            if (count($callback) != 2 OR !is_object($callback[0]))
            {
                throw new RuntimeException("ValidationChain error in running validator callback. The callback must be either anonymous function or an array of object reference and method name (static calls not allowed).");
            }
            
            $callbackObject = $callback[0];
            $callbackMethod = $callback[1];
            return $callbackObject->$callbackMethod(
                $this->activeValueID,
                $this->values[$this->activeValueID],
                $args
            );
        }
        
        return $callback(
            $this->activeValueID,
            $this->values[$this->activeValueID],
            $args
        );
    }
    
    /**
     * Update values with newly transformed ones.
     * 
     * @see validate()
     * @param ValidatorResult $result The result object from a validator method.
     *
     */
    protected function updateValue(ValidatorResult $result)
    {
        if ($result->hasNewValue())
        {
            $this->value = $result->getValue();
        }
    }
    
    /**
     * Add the messages returned from the validator method.
     * 
     * Appends the message to the class's $messages property.
     * 
     * @see validate()
     * @param ValidatorResult $result The result object from a validator method.
     * 
     */
    protected function addErrorMessages(ValidatorResult $result)
    {
        foreach ($result->getErrorMessages() as $message)
        {
            $message->attachTo($this->activeValueID);
            $this->errorMessages[] = $message;
        }
    }
}