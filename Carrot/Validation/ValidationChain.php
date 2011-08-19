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
 * Represents chains of validation processes. This class is used
 * to validate a set of values using validator callbacks that
 * may be either an anonymous function or an array containing an
 * object reference and a method name. This class is meant to be
 * used inside your model's method.
 *
 * You can either construct this object directly on your model
 * method (thereby encapsulating the validation behavior in your
 * domain model class) or you can have it injected to your model,
 * whichever suits you better.
 * 
 * This class comes with a set of default validators. You can
 * inject your custom validators via the constructor or via the
 * setter method. Your custom validator class must implement
 * {@see ValidatorInterface}.
 * 
 * Before starting the validation chain, you must first set the
 * values to be validated (along with their value ID):
 * 
 * <code>
 * $chain = new ValidatorChain;
 * $chain->setValues(array(
 *     'username' => $username,
 *     'password' => $password
 * ));
 * </code>
 * 
 * You can start the validation by starting the chain. You cannot
 * run validator methods properly without explicitly starting and
 * stopping the chain. Example of a chain:
 * 
 * <code>
 * $chain->start('username')
 *       ->validate('string.maxLength', 9)
 *       ->validate('string.alphanumeric')
 *       ->stop();
 * </code>
 *
 * Since the state of the chain is the state of the class, you can
 * start chains without resorting to method chaining. So this
 * example will also work:
 *
 * <code>
 * $chain->start('username')
 * $chain->validate('basic.string.maxLength', 9)
 * $chain->validate('basic.string.alphanumeric')
 * $chain->stop();
 * </code>
 * 
 * After calling the stop() method, the ValidationChain instance
 * can be reused to validate the rest of the values. After
 * validation, you can check whether the values passes or not:
 *
 * <code>
 * if (!$chain->passesValidation())
 * {
 *     // Show error page
 * }
 *
 * // Show success page or continue processing
 * </code>
 * 
 * Get an array containing instances of MessageInterface returned
 * by validators, representing messages the validators would like
 * to display to the user. These messages should be tied to the
 * relevant value ID. Ideally you should have a mediator
 * object that takes these messages and maps them to their
 * respective form fields {@see MessageInterface}:
 * 
 * <code>
 * $messages = $chain->getMessages();
 * </code>
 * 
 * If the validation is successful, don't forget to get the
 * values back from the ValidationChain instance.
 * 
 * <code>
 * // Immediately replace the values
 * extract($chain->getValues());
 * </code>
 * 
 * The reason you need to get the new values is that
 * some validators might perform data cleaning/normalization. Read
 * each individual validator's docs for more info.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation;

use RuntimeException;
use InvalidArgumentException;
use Carrot\Message\MessageInterface;
use Carrot\Validation\Validator\ValidatorInterface;
use Carrot\Validation\Validator\ExistenceValidator;
use Carrot\Validation\Validator\NumberValidator;
use Carrot\Validation\Validator\StringValidator;
use Carrot\Validation\Validator\TypeValidator;
use Carrot\Validation\Validator\ComparisonValidator;

class ValidationChain
{
    /**
     * @var array List of values to be validated, sorted by their value ID.
     */
    protected $values = array();
    
    /**
     * @var array Instances of MessageInterface returned by validator methods.
     */
    protected $messages = array();
    
    /**
     * @var status TRUE if all validations return valid results, FALSE if there is at least one invalid result.
     */
    protected $passesValidation = TRUE;
    
    /**
     * @var array Contains validator callbacks with their validator IDs as index.
     */
    protected $callbacks = array();
    
    /**
     * @var bool TRUE if the chain is currently started, FALSE otherwise.
     */
    protected $chainStarted = FALSE;
    
    /**
     * @var string The ID of the value currently active in the chain, could be NULL.
     */
    protected $chainActiveValueID;
    
    /**
     * @var bool TRUE if the current chain is optional, FALSE otherwise.
     */
    protected $chainIsOptional = FALSE;
    
    /**
     * @var bool If set to TRUE, validate() will ignore calls, exists to make optional chains possible.
     */
    protected $ignoreChain = FALSE;
    
    /**
     * Constructor.
     * 
     * If constructed without arguments, this class will initialize
     * all default validator classes. On the other hand, if you inject
     * an array of validators at construction, the constructor will
     * not try to register default validators.
     *
     * <code>
     * $chain = new ValidatorChain(array(
     *     $businessRuleValidator,
     *     $decimalValidator,
     *     $ISBNValidator
     * ));
     * </code>
     * 
     * @param array $customValidators Contains list of custom validators to add.
     * 
     */
    public function __construct(array $validators = array())
    {
        if (empty($validators))
        {
            $validators = array(
                new ExistenceValidator,
                new NumberValidator,
                new StringValidator,
                new ComparisonValidator,
                new TypeValidator
            );
        }
        
        foreach ($validators as $validator)
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
     * $validatorChain->registerValidator($businessRuleValidator);
     * $validatorChain->registerValidator($decimalValidator);
     * $validatorChain->registerValidator($ISBNValidator);
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
     * Starts the chain.
     * 
     * This method starts a validation chain. You must run this method
     * each time you want to start a new validation chain. Pass the
     * ID of the main value to be validated.
     *
     * <code>
     * $chain->start('username')
     *       ->validate('string.maxLength', 10)
     *       ->validate('string.minLength', 5)
     *       ->validate('string.alphanumeric')
     *       ->stop();
     * </code>
     *
     * Call stop() when you wanted to finish the chain. Technically
     * starting the chain again would reset the state of this class,
     * but you are recommended to run this method regardless because
     * of readability reasons.
     * 
     * @see stop()
     * @see startOptional() 
     * @param string $activeValueID The ID of the value to validate.
     * @return ValidationChain This object itself.
     * 
     */
    public function start($activeValueID = NULL)
    {
        if ($activeValueID != NULL AND !array_key_exists($activeValueID, $this->values))
        {
            throw new InvalidArgumentException("ValidationChain error when trying to start chain. The value ID '{$activeValueID}' is not set.");
        }
        
        $this->chainStarted = TRUE;
        $this->chainActiveValueID = $activeValueID;
        $this->chainIsOptional = FALSE;
        $this->ignoreChain = FALSE;
        return $this;
    }
    
    /**
     * Starts the chain, but stops if the first validation fails.
     * 
     * Useful when you have to validate optional variables. When you
     * start a chain with this method, the result of the first
     * validation on the chain will be used to determine whether or
     * not to continue validating.
     * 
     * If the result of the first validation is valid, the chain will
     * continue to run exactly like a regular chain. Otherwise, if
     * the result of the first validation is invalid:
     *
     * <ul>
     *     <li>
     *         The rest of the chain will be ignored.
     *     </li>
     *     <li>
     *         Messages from the result are note saved.
     *     </li>
     *     <li>
     *         Value value changes will not be conducted.
     *     </li>
     *     <li>
     *         The invalid result returned is discarded and doesn't
     *         affect the return value of passesValidation().
     *     </li>
     * </ul>
     *
     * Example usage (validating an optional birthday value):
     *
     * <code>
     * $chain->startOptional('birthday')
     *       ->validate('existence.notEmpty')
     *       ->validate('date.validDateString')
     *       ->stop();
     * </code>
     * 
     * @param string $activeValueID The ID of the value to validate.
     * @return ValidationChain This object itself.
     * 
     */
    public function startOptional($activeValueID = NULL)
    {
        if ($activeValueID != NULL AND array_key_exists($activeValueID, $this->values))
        {
            throw new InvalidArgumentException("ValidationChain error when trying to start optional chain. The value ID '{$activeValueID}' is not set.");
        }
        
        $this->chainStarted = TRUE;
        $this->chainActiveValueID = $activeValueID;
        $this->chainIsOptional = TRUE;
        $this->ignoreChain = FALSE;
        return $this;
    }
    
    /**
     * Stops the chain, returns everything to normal state.
     * 
     * Calling this method is optional, as calling start() once more
     * will also reset the the chain state. However, calling this
     * method explicitly may make the chaining code easier to read.
     * 
     */
    public function stop()
    {
        $this->chainStarted = FALSE;
        $this->chainActiveValueID = NULL;
        $this->chainIsOptional = FALSE;
        $this->ignoreChain = FALSE;
    }
    
    /**
     * Run a validator.
     * 
     * Runs the validator callback and process its result object. Will
     * add messages and updates values with new, transformed values regardless
     * of whether the result is valid or invalid (except if the chain is optional).
     * 
     * To use, first start the chain {@see start()}:
     *
     * <code>
     * $chain->start('username')
     *       ->validate('string.maxLength', 10)
     *       ->validate('string.minLength', 5)
     *       ->validate('string.alphanumeric')
     *       ->stop();
     * </code>
     * 
     * What the second argument represents depends on the validator
     * method. The second argument for 'string.maxLength', for
     * example, must be an integer and represents the maximum string
     * length for the value being validated. Read the docs of each
     * validator so you know what you need to send. Validators should
     * throw relevant exceptions if the arguments provided is invalid.
     *
     * Normally, validation will still run even after an invalid
     * result is returned. This behavior can be overridden by passing
     * TRUE for the third argument, $breakChainOnFailure:
     *
     * <code>
     * // Stop chain if an invalid result is returned
     * $chain->start('username')
     *       ->validate('existence.notEmpty', NULL, TRUE)
     *       ->validate('string.MinLength', 5, TRUE)
     *       ->validate('string.MaxLength', 10, TRUE)
     *       ->stop();
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
        if (!$this->chainStarted)
        {
            throw new RuntimeException("ValidationChain error when trying to validate. You cannot start validating before the chain is started.");
        }
        
        if ($this->ignoreChain)
        {
            return;
        }
        
        if (!array_key_exists($validatorID, $this->callbacks))
        {
            throw new InvalidArgumentException("ValidationChain error when trying to validate. Validator with the ID '{$validatorID}' does not exist.");
        }
        
        $result = $this->runValidatorCallback($validatorID, $args);
        
        if (!is_object($result) OR !($result instanceof ValidationResult))
        {
            throw new RuntimeException("ValidationChain error when trying to validate. Validator with the ID '{$validatorID}' does not return an instance of Carrot\Validation\ValidationResult.");
        }
        
        // If this is an optional chain and the result is
        // valid, the chain is no longer optional and must
        // be completed.
        if ($result->isValid() AND $this->chainIsOptional)
        {
            $this->chainIsOptional = FALSE;
        }
        
        // If this is an optional chain and the result is
        // invalid, we must ignore the rest of the chain
        // and we must not add the messages or change the
        // value of $valid class property.
        if (!$result->isValid() AND $this->chainIsOptional)
        {
            $this->ignoreChain = TRUE;
            return $this;
        }
        
        if (!$result->isValid())
        {
            $this->passesValidation = FALSE;
            
            if ($breakChainOnFailure)
            {
                $this->ignoreChain = TRUE;
            }
        }
        
        $this->updateValues($result);
        $this->addMessages($result);
        return $this;
    }
    
    /**
     * Sets values to validate.
     * 
     * Before you can start a validation chain, you have to set the
     * values to validate first. Each value must have an ID,
    // ---------------------------------------------------------------
     * which will later be used by the returned messages to attach
     * itself to a particular field and refering to the
     * field's label string.
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
     * Get the validated values.
     * 
     * The returned values might differ with the ones originally
     * provided because validator methods can tell validator chain to
     * transform the value after cleaning/conversion/
     * normalization of the value. Hence, you can use this method to
     * get the transformed values after validation.
     *
     * <code>
     * // Immediately replace the values
     * extract($chain->getValues());
     * </code>
     * 
     * @see ValidationResult::changeValue
     * @return array Validated values with their value ID 
     * 
     */
    public function getValues()
    {
        return $this->values;
    }
    
    /**
     * Get messages returned from validation methods in an array.
     * 
     * Please note that these messages may not be all error messages.
     * This class does not filter instances of MessageInterface
     * returned by the validators. Default validators will always
     * return instance of ErrorMessage, but custom validators might
     * vary in their approach (they might return a valid result but
     * include a warning message, for example).
     *
     * Example returned array:
     *
     * <code>
     * $messages = array(
     *     $errorMessageA,
     *     $errorMessageB,
     *     $warningMessage,
     *     $errorMessageC
     * );
     * </code>
     * 
     * @return array Contains MessageInterface instances.
     * 
     */
    public function getMessages()
    {
        return $this->messages;
    }
    
    /**
     * Checks if values successfully passed all validations or not.
     *
     * Use this method to determine the next steps after you have
     * finished running your validation chains.
     *
     * <code>
     * if (!$chain->passesValidation())
     * {
     *     // Show error page
     * }
     *
     * // Show success page or continue processing
     * </code>
     * 
     * 
     * @return bool TRUE if all validations return valid results, FALSE if there is at least one invalid result.
     * 
     */
    public function passesValidation()
    {
        return $this->passesValidation;
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
        
        if (array_key_exists($validatorID, $this->callbacks))
        {
            throw new InvalidArgumentException("ValidationChain error in adding validator callback. The validator ID '{$validatorID}' has already been defined.");
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
            $callbackObject = $callback[0];
            $callbackMethod = $callback[1];
            return $callbackObject->$callbackMethod(
                $this->chainActiveValueID,
                $this->values,
                $args
            );
        }
        
        return $callback(
            $this->chainActiveValueID,
            $this->values,
            $args
        );
    }
    
    /**
     * Update values with newly transformed ones.
     * 
     * This method replaces the old values with newly
     * transformed/cleaned/normalized values returned via
     * the ValidationResult object. If the new value ID
     * doesn't already exist in this class's $values class
     * property, the new value is discarded, hence updating values
     * cannot create new value.
     * 
     * @see validate()
     * @param ValidationResult $result The result object from a validator method.
     *
     */
    protected function updateValues(ValidationResult $result)
    {
        foreach ($result->getNewValues() as $valueID => $value)
        {
            if (array_key_exists($valueID, $this->values))
            {
                $this->values[$valueID] = $value;
            }
        }
    }
    
    /**
     * Add the messages returned from the validator method.
     * 
     * Appends the message to the class's $messages property.
     * 
     * @see validate()
     * @param ValidationResult $result The result object from a validator method.
     * 
     */
    protected function addMessages(ValidationResult $result)
    {
        foreach ($result->getMessages() as $message)
        {
            $this->messages[] = $message;
        }
    }
}