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
// ---------------------------------------------------------------
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form;

use InvalidArgumentException;
use Carrot\Core\Request;
use Carrot\Validation\ValidationMessageInterface;
use Carrot\Form\Field\FieldInterface;

class FormDefinition
{
    /**
     * @var array Contains instances of FieldInterface.
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
     * @var string The encoding type of this form, defaults to form URL encoded.
     */
    protected $enctype;
    
    /**
     * @var array List of fieldset labels and the FieldInterface instances that belongs to this fieldset.
     */
    protected $fieldsets = array();
    
    /**
     * @var type comments
     */
    protected $fieldsInFieldsets = array();
    
    /**
     * @var FormRendererInterface Used to render this form.
     */
    protected $renderer;
    
    /**
     * Constructor.
     * 
     * 
     * 
     * @param array $fields
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
     * 
     * 
     * @param Field $field Instance of Field.
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
     * Checks if form submission is valid.
     * 
    // ---------------------------------------------------------------
     * The form submission of the current request is considered valid
     * all fields are able to process request array and return the
     * appropriate request variable value. 
     * 
     * @param Request $request Instance of Request, to get the request array.
     * @return bool TRUE if the form request is valid, FALSE otherwise.
     * 
     */
    public function isSubmissionValid(Request $request)
    {
        $formSubmissionArray = $this->getFormSubmissionArray($request);
        
        foreach ($this->fields as $field)
        {   
            if (!$field->isSubmissionValid($formSubmissionArray))
            {
                return FALSE;
            }
        }
        
        return TRUE;
    }
    
    /**
     * Adds a fieldset (a group of fields).
     * 
    // ---------------------------------------------------------------
     * If the 
     * 
     * @param string $label The label of the fieldset (also acts as its ID).
     * @param array $fieldIDs IDs of the fields belonging to this fieldset.
     * 
     */
    public function addFieldset($label, array $fieldIDs)
    {
        foreach ($fieldIDs as $fieldID)
        {
            if (!array_key_exists($fieldID, $this->fields))
            {
                throw new InvalidArgumentException("FormDefinition error in adding fieldset with the label '{$label}'. The field '{$fieldID}' does not exist.");
            }
            
            $field = $this->fields[$fieldID];
            $this->fieldsets[$label][] = $field;
            $this->fieldsInFieldsets[] = $fieldID;
        }
    }
    
    /**
     * Set error messages for all fields.
     * 
    // ---------------------------------------------------------------
     * This method ignores the MessageInterface instance if it is not
     * attached to any of the field ID of this form.
     * 
     * @param array $messages Contains MessageInterface implementations.
     * 
     */
    public function addValidationMessages(array $messages, array $valueToFieldMap = array())
    {
        $labels = $this->getLabelsFromMap($valueToFieldMap);
        
        foreach ($messages as $message)
        {
            if (!is_object($message) OR !($message instanceof ValidationMessageInterface))
            {
                continue;
            }
            
            $currentValueID = $message->getValueID();
            $fieldID = $this->getFieldIDFromMap($currentValueID, $valueToFieldMap);
            
            if ($fieldID != FALSE AND array_key_exists($fieldID, $this->fields))
            {
                $message->setLabels($labels);
                $field = $this->fields[$fieldID];
                $field->addErrorMessage($message->get());
            }
        }
    }
    
    public function addErrorMessages($fieldID, array $messages)
    {
        if (!array_key_exists($fieldID, $this->fields))
        {
            throw new InvalidArgumentException("FormDefinition error in adding error messages. The field '{$fieldID}' does not exist.");
        }
        
        foreach ($messages as $message)
        {
            $message = (string) $message;
            $this->fields[$fieldID]->addErrorMessage($message);
        }
    }
    
    /**
     * Set default values for this request.
     * 
     * @param Request $request 
     * 
     */
    public function setDefaultValues(Request $request)
    {
        $formSubmissionArray = $this->getFormSubmissionArray($request);
        
        foreach ($this->fields as $field)
        {
            $defaultValue = $field->getValue($formSubmissionArray);
            $field->setDefaultValue($defaultValue);
        }
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    public function getSubmittedValue($fieldID, Request $request)
    {
        if (!array_key_exists($fieldID, $this->fields))
        {
            throw new InvalidArgumentException("FormDefinition error in getting value submitted. The field '{$fieldID}' does not exist.");
        }
        
        $formSubmissionArray = $this->getFormSubmissionArray($request);
        return $this->fields[$fieldID]->getValue($formSubmissionArray);
    }
    
    /**
     * Get the list of field labels
     *
     */
    public function getFieldLabels()
    {
        return $this->labels;
    }
    
    /**
     * Gets fieldsets, along with FieldInterface instances that belong the them.
     * 
     * 
     * 
     */
    public function getFieldsets()
    {
        return $this->fieldsets;
    }
    
    public function getFieldset($label)
    {
        if (!array_key_exists($label, $this->fieldsets))
        {
            throw new InvalidArgumentException("FormDefinition error in getting fieldset. Fieldset with the label '{$label}' does not exist.");
        }
        
        return $this->fieldsets[$label];
    }
    
    /**
     * Get instances of FieldInterface 
     * 
     * 
     * 
     */
    public function getFields()
    {
        return $this->fields;
    }
    
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
    
    public function getField($fieldID)
    {
        if (!array_key_exists($fieldID, $this->fields))
        {
            throw new InvalidArgumentException("FormDefinition error in getting field. Field with ID '{$fieldID}' does not exist.");
        }
        
        return $this->fields[$fieldID];
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
    
    protected function getLabelsFromMap(array $valueToFieldMap)
    {
        // If no map provided, assume that the value ID
        // is exactly the same as the field ID.
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
    
    protected function getFieldIDFromMap($valueID, $valueToFieldMap)
    {
        // If no map provided, assume that the value ID
        // is exactly the same as the field ID.
        if (empty($valueToFieldMap))
        {
            return $valueID;
        }
        
        if ($valueID === FALSE)
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