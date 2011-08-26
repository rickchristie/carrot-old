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
 * Form Definition
 * 
 * Represents a form, contains information about the form, fields
 * and fieldsets. After the form is defined, we can tell it to get
 * values from request variable, check if the current form
 * submission is valid, etc.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form;

use InvalidArgumentException;
use Carrot\Core\Request;
use Carrot\Message\ValidationErrorMessageInterface;
use Carrot\Form\Field\FieldInterface;

class FormDefinition
{
    /**
     * @var array Fields of this form. Contains instances of FieldInterface.
     */
    protected $fields = array();
    
    /**
     * @var array List of labels with their field IDs as index. To be used later in adding error messages.
     */
    protected $labels = array();
    
    /**
     * @var string The method of this form, can be either 'get' or 'post'. Defaults to 'post'.
     */
    protected $method;
    
    /**
     * @var string The encoding type of this form, defaults to 'application/x-www-form-urlencoded'.
     */
    protected $enctype;
    
    /**
     * @var array List of fieldset legends and the FieldInterface instances that are grouped to the fieldset.
     */
    protected $fieldsets = array();
    
    /**
     * @var array Contains IDs of fields attached to a fieldset.
     */
    protected $fieldsInFieldsets = array();
    
    /**
     * @var array Contains ValidationErrorMessageInterface instances attached to a field.
     */
    protected $fieldValidationErrorMessages = array();
    
    /**
     * @var array Contains ValidationErrorMessageInterface instances not attached to any field, thus is general to the form.
     */
    protected $formValidationErrorMessages = array();
    
    /**
     * Constructor.
     * 
     * Writes default values for method and enctype ('post' and
     * 'application/x-www-form-urlencoded' respectively).
     * 
     */
    public function __construct()
    {
        $this->setMethodToPost();
        $this->setEnctypeToFormURLEncoded();
    }
    
    /**
     * Add a field to this form.
     * 
     * Add as many fields as you need:
     * 
     * <code>
     * $form->addField($usernameField);
     * $form->addField($passwordField);
     * </code>
     * 
     * @param Field $field Instance of FieldInterface.
     *
     */
    public function addField(FieldInterface $field)
    {
        $id = $field->getID();
        $label = $field->getLabel();
        $this->labels[$id] = $label;
        $this->fields[$id] = $field;
    }
    
    /**
     * Adds a fieldset (a group of fields).
     * 
     * To create a fieldset, pass a legend string and an array
     * containing the IDs of the field that is supposed to be grouped
     * in that fieldset:
     * 
     * <code>
     * $form->addFieldset('Your Profile', array(
     *     'firstName',
     *     'lastName',
     *     'birthDate',
     *     'birthPlace',
     *     'city'
     * )):
     * </code>
     * 
     * Throws InvalidArgumentException if the field ID is already
     * registered to another fieldset.
     * 
     * Throws InvalidArgumentException if the field ID doesn't exist.
     * 
     * @throws InvalidArgumentException
     * @param string $legend The legend of the fieldset (also acts as its ID).
     * @param array $fieldIDs IDs of the fields belonging to this fieldset.
     * 
     */
    public function addFieldset($legend, array $fieldIDs)
    {
        foreach ($fieldIDs as $fieldID)
        {
            if (!array_key_exists($fieldID, $this->fields))
            {
                throw new InvalidArgumentException("FormDefinition error in adding fieldset with the legend '{$legend}'. The field '{$fieldID}' does not exist.");
            }
            
            if (in_array($fieldID, $this->fieldsInFieldsets))
            {
                throw new InvalidArgumentException("FormDefinition error in adding fieldset with the legend '{$legend}'. The field '{$fieldID}' is already registered to another fieldset.");
            }
            
            $field = $this->fields[$fieldID];
            $this->fieldsets[$legend][$fieldID] = $field;
            $this->fieldsInFieldsets[] = $fieldID;
        }
    }
    
    /**
     * Set error messages for all fields.
     * 
     * Loops through the ValidationErrorMessageInterface instances,
     * sets the labels of each instance and adds the message to
     * corresponding fields.
     * 
     * If the message isn't attached to any field ID, it will be
     * considered as a message to the whole form. Renderers can still
     * access these messages using
     * {@see getFieldValidationErrorMessages()} and
     * {@see getFormValidationErrorMessages()} methods.
     * 
     * Since the validation (domain) is not aware of the form
     * (presentation), the validation attaches its messages to
     * validation value IDs instead of field IDs. Most of the times,
     * we can have our field IDs match their value ID counterparts. In
     * the unfortunate event where our field IDs differs with their
     * validation value ID counterparts, we have to provide a mapping
     * array so that the replacements of value label placeholders and
     * the attachment of message to a field is done correctly.
     * 
     * The value-to-field map is a simple array, with value ID as the
     * index and field ID as the content:
     * 
     * <code>
     * $form->addValidationErrorMessages($messages, array(
     *     'user' => 'username',
     *     'pwd' => 'password'
     * ));
     * </code>
     * 
     * @param array $messages Contains ValidationErrorMessageInterface instances.
     * @param array $valueToFieldMap Contains an array that maps value IDs to field IDs.
     * 
     */
    public function addValidationErrorMessages(array $messages, array $valueToFieldMap = array())
    {
        $labels = $this->getLabelsFromMap($valueToFieldMap);
        
        foreach ($messages as $message)
        {
            if (!is_object($message) OR !($message instanceof ValidationErrorMessageInterface))
            {
                continue;
            }
            
            $valueID = $message->getValueID();
            $fieldID = $this->convertToFieldID($valueID, $valueToFieldMap);
            $message->setLabels($labels);
            
            if ($fieldID != FALSE AND array_key_exists($fieldID, $this->fields))
            {
                $this->fields[$fieldID]->addErrorMessage($message);
                $this->fieldValidationErrorMessages[$fieldID][] = $message;
                continue;
            }
            
            $this->formValidationErrorMessages[] = $message;
        }
    }
    
    /**
     * Get instances of ValidationErrorMessageInterface that is attached to a field.
     * 
     * Useful in rendering a summary of the field validation error
     * messages. This method returns only messages that is attached to
     * a field.
     *
     * Example returned array:
     *
     * <code>
     * $fieldErrors = array(
     *     'fieldID' => array(
     *         $validationErrorMessageA,
     *         $validationErrorMessageB
     *     ),
     *     'username' => array(
     *         $validationErrorMessageA,
     *         $validationErrorMessageB,
     *         $validationErrorMessageC
     *     ),
     *     ...
     * );
     * </code>
     * 
     * @see getFormValidationErrorMessages()
     * @return array Instances of ValidationErrorMessageInterface.
     *
     */
    public function getFieldValidationErrorMessages()
    {
        return $this->fieldValidationErrorMessages;
    }
    
    /**
     * Get instances of ValidationErrorMessageInterface that isn't attached to any field.
     * 
     * Useful in rendering a summary of general validation error
     * messages. This method returns only messages that is NOT
     * attached to any field, thus the message applies 'generally' to
     * the entire form.
     *
     * Example returned array:
     *
     * <code>
     * $formErrors = array(
     *     $validationErrorMessageA,
     *     $validationErrorMessageB,
     *     $validationErrorMessageC,
     *     ...
     * );
     * </code>
     * 
     * @see getFieldValidationErrorMessages()
     * @return array Contains ValidationErrorMessageInterface instances.
     *
     */
    public function getFormValidationErrorMessages()
    {
        return $this->formValidationErrorMessages;
    }
    
    /**
     * Checks if form submission is valid.
     * 
     * Loops through all the fields and tells them to check whether
     * the form submission is valid or not. If one of them returns
     * FALSE, it returns FALSE immediately. Otherwise, if all fields
     * returns TRUE, this method returns TRUE.
     * 
     * @param Request $request Instance of Request, to get the request array.
     * @return bool TRUE if the form request is valid, FALSE otherwise.
     * 
     */
    public function isSubmissionValid(Request $request)
    {
        $formSubmissionArray = $this->getFormSubmissionArray($request);
        $fileSubmissionArray = $request->getFiles();
        
        foreach ($this->fields as $field)
        {
            if (!$field->isSubmissionValid($formSubmissionArray, $fileSubmissionArray))
            {
                return FALSE;
            }
        }
        
        return TRUE;
    }
    
    /**
     * Set default values for this request.
     * 
     * Loops through all the fields. Calls FieldInterface::getValue()
     * to get the value from the request, then sets it as the default
     * value using FieldInterface::setDefaultValue().
     * 
     * @param Request $request Used to get form submission array.
     * 
     */
    public function setDefaultValues(Request $request)
    {
        $formSubmissionArray = $this->getFormSubmissionArray($request);
        $fileSubmissionArray = $request->getFiles();
        
        foreach ($this->fields as $field)
        {
            $defaultValue = $field->getValue($formSubmissionArray, $fileSubmissionArray);
            $field->setDefaultValue($defaultValue);
        }
    }
    
    /**
     * Gets submitted value.
     * 
     * Acts as a wrapper to FieldInterface::getValue().
     * 
     * Throws InvalidArgumentException if the field ID doesn't exist.
     * 
     * @throws InvalidArgumentException
     * @param string $fieldID The ID of the field whose value you wanted to get.
     * @param Request $request Used to get the form submission array.
     *
     */
    public function getSubmittedValue($fieldID, Request $request)
    {
        if (!array_key_exists($fieldID, $this->fields))
        {
            throw new InvalidArgumentException("FormDefinition error in getting value submitted. The field '{$fieldID}' does not exist.");
        }
        
        $field = $this->fields[$fieldID];
        $formSubmissionArray = $this->getFormSubmissionArray($request);
        $fileSubmissionArray = $request->getFiles();
        return $field->getValue($formSubmissionArray, $fileSubmissionArray);
    }
    
    /**
     * Get the list of field labels, along with their field IDs.
     * 
     * Example returned array structure:
     * 
     * <code>
     * $labels = array(
     *     'username' => 'User Name',
     *     'password' => 'Password'
     * );
     * </code>
     * 
     * @return array Field labels with field IDs as their array index.
     *
     */
    public function getFieldLabels()
    {
        return $this->labels;
    }
    
    /**
     * Gets fieldsets, along with FieldInterface instances that belong the them.
     * 
     * Example returned array structure:
     *
     * <code>
     * $fieldsets = array(
     *     'User Profile' => array(
     *         'fieldID' => $field,
     *         'firstName' => $firstNameField,
     *         'lastName' => $lastNameField,
     *         'birthDate' => $birthDateField
     *     ),
     *     'Social' => array(
     *         $facebookLinkField,
     *         $twitterLinkField
     *     ),
     *     ...
     * );
     * </code>
     * 
     * @return array Fieldsets array.
     * 
     */
    public function getFieldsets()
    {
        return $this->fieldsets;
    }
    
    /**
     * Get a fieldset array with the given legend.
     * 
     * The returned array structure:
     *
     * <code>
     * $fieldset = array(
     *     $firstNameField,
     *     $lastNameField,
     *     $birthDateField
     * );
     * </code>
     * 
     * Throws InvalidArgumentException if the fieldset doesn't exist.
     * 
     * @throws InvalidArgumentException
     * @param string $legend The legend of the fieldset to get.
     * @return array The fieldset array.
     *
     */
    public function getFieldset($legend)
    {
        if (!array_key_exists($legend, $this->fieldsets))
        {
            throw new InvalidArgumentException("FormDefinition error in getting fieldset. Fieldset with the label '{$legend}' does not exist.");
        }
        
        return $this->fieldsets[$legend];
    }
    
    /**
     * Get instances of FieldInterface 
     * 
     * The returned array structure:
     *
     * <code>
     * $fields = array(
     *     'fieldID' => $field,
     *     'username' => $usernameField,
     *     'password' => $passwordField,
     *     ...
     * );
     * </code>
     * 
     * @return array Contains instances of FieldInterface.
     * 
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * Get fields not grouped in any fieldsets.
     * 
     * This method is particularly useful if your form contains
     * fieldsets and some fields are not grouped in any fieldset. You
     * can use this method to get the rest of the fields after
     * you've rendered the fieldsets.
     *
    // ---------------------------------------------------------------
     * Example returned array:
     *
     * <code>
     * $fieldsNotInFieldsets = array(
     *     'fieldID' => $field,
     *     'username' => $usernameField,
     *     'password' => $passwordField,
     *     ...
     * );
     * </code>
     * 
     * @return array Contains instances of FieldInterface.
     *
     */
    public function getFieldsNotInFieldsets()
    {
        $fieldsNotInFieldsets = array();
        
        foreach ($this->fields as $fieldID => $field)
        {
            if (!in_array($fieldID, $this->fieldsInFieldsets))
            {
                $fieldsNotInFieldsets[$fieldID] = $field;
            }
        }
        
        return $fieldsNotInFieldsets;
    }
    
    /**
     * Get the FieldInterface instance with the given ID.
     *
     * @return FieldInterface The instance of field with the given field ID.
     *
     */
    public function getField($fieldID)
    {
        if (!array_key_exists($fieldID, $this->fields))
        {
            throw new InvalidArgumentException("FormDefinition error in getting field. Field with ID '{$fieldID}' does not exist.");
        }
        
        return $this->fields[$fieldID];
    }
    
    /**
     * Check if a field with the given ID exists.
     * 
     * @return bool TRUE if the field exists, FALSE otherwise.
     *
     */
    public function fieldExists($fieldID)
    {
        return (array_key_exists($fieldID, $this->fields));
    }
    
    /**
     * Set method of this form to 'post'.
     *
     */
    public function setMethodToPost()
    {
        $this->method = 'post';
    }
    
    /**
     * Set method of this form to 'get'.
     *
     */
    public function setMethodToGet()
    {
        $this->method = 'get';
    }
    
    /**
     * Returns the method of this form.
     *
     * @return string Can be either 'get' or 'post'.
     *
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * Set the encoding type of this form to 'application/x-www-form-urlencoded'.
     * 
     */
    public function setEnctypeToFormURLEncoded()
    {
        $this->enctype = 'application/x-www-form-urlencoded';
    }
    
    /**
     * Set the encoding type of this form to 'multipart/form-data'.
     *
     */
    public function setEnctypeToMultipart()
    {
        $this->enctype = 'multipart/form-data';
    }
    
    /**
     * Returns the encoding type of this form.
     *
     * @return string Either 'application/x-www-form-urlencoded' or 'multipart/form-data'.
     *
     */
    public function getEnctype()
    {
        return $this->enctype;
    }
    
    /**
     * Get the request array from the Request instance.
     * 
     * If the form's method is 'post' will get and return POST array
     * from the Request instance. Otherwise, will get and return GET
     * array from the Request instance.
     * 
     * @param Request $request Instance of Request.
     * @return array The form submission array.
     * 
     */
    protected function getFormSubmissionArray(Request $request)
    {
        if ($this->method == 'post')
        {
            return $request->getPost();
        }
        
        return $request->getGet();
    }
    
    /**
     * Get labels array with the value ID to field ID map given.
     * 
     * Because the messages from the validation are attached to
     * validation value IDs instead of field IDs, we have to reformat
     * the labels array so that the index of the labels array uses
     * value IDs instead of usual field IDs.
     *
     * If an empty value-to-field map array is given, the method
     * assumes that the value ID is the same as the field ID and
     * returns unformatted $labels class property.
     * 
     * Throws InvalidArgumentException if the value-to-field mapping
     * array doesn't contain mapping for all of the registered fields.
     * 
     * @see addValidationErrorMessages()
     * @throws InvalidArgumentException
     * @param array $valueToFieldMap The value ID to field ID mapping array.
     * @return array The labels array.
     *
     */
    protected function getLabelsFromMap(array $valueToFieldMap)
    {
        if (empty($valueToFieldMap))
        {
            return $this->labels;
        }
        
        $labelsBuilt = array();
        
        foreach ($this->labels as $fieldID => $label)
        {
            $valueID = array_search($fieldID, $valueToFieldMap, TRUE);
            
            if ($valueID === FALSE)
            {
                throw new InvalidArgumentException("FormDefinition error when trying to add validator messages. The value-to-field map given doesn't have mappings for field '{$fieldID}'.");
            }
            
            $labelsBuilt[$valueID] = $label;
        }
        
        return $labelsBuilt;
    }
    
    /**
     * Convert value ID given to field ID.
     * 
     * The conversion is done using the information given from the
     * value ID to field ID mapping array given. Returns FALSE if the
     * value ID doesn't exist or the value ID is invalid.
     *
     * If an empty value-to-field map array is given, this method
     * assumes that the value ID is the same as the field ID, and
     * simply returns the value ID given.
     *
     * @param string $valueID The value ID to transform to field ID.
     * @param array $valueToFieldMap The value ID to field ID mapping array.
     * @return string|FALSE The field ID, or FALSE if it can't find it.
     *
     */
    protected function convertToFieldID($valueID, array $valueToFieldMap)
    {
        if (empty($valueToFieldMap))
        {
            return $valueID;
        }
        
        if (!is_string($valueID))
        {
            return FALSE;
        }
        
        if (!array_key_exists($valueID, $valueToFieldMap))
        {
            return FALSE;
        }
        
        return $valueToFieldMap[$valueID];
    }
}