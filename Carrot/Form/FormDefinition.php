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

use Carrot\Core\Request;
use Carrot\Message\MessageInterface;

class FormDefinition
{
    /**
     * @var array Contains instances of Parameter.
     */
    protected $parameters = array();
    
    /**
     * @var array List of labels with their parameter IDs as index. To be used later in adding error messages.
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
     * @var array List of fieldset labels and the parameters that belongs to this fieldset.
     */
    protected $fieldsets = array();
    
    /**
     * Constructor.
     * 
     * 
     * 
     * @param array $parameters
     * 
     */
    public function __construct(array $parameters = array())
    {
        $this->setMethodToPost();
        $this->setEnctypeToFormURLEncoded();
        
        foreach ($parameters as $parameter)
        {
            $this->addParameter($parameter);
        }
    }
    
    /**
     * Add a parameter to this form.
     * 
     * 
     * 
     * @param Parameter $parameter Instance of Parameter.
     *
     */
    public function addParameter(Parameter $parameter)
    {
        $id = $parameter->getID();
        $label = $parameter->getLabel();        
        $this->labels[$id] = $label;
        $this->parameter[$id]['object'] = $parameter;
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
    public function submissionIsValid(Request $request)
    {
        $requestArray = $this->getRequestArray($request);
        
        foreach ($this->parameters as $parameter)
        {
            $field = $parameter->getField();
            
            if (!$field->canReturnRequestVariableValue())
            {
                return FALSE;
            }
        }
        
        return TRUE;
    }
    
    /**
     * Adds a fieldset (a group of parameters).
     * 
    // ---------------------------------------------------------------
     * If the 
     * 
     * @param string $label The label of the fieldset (also acts as its ID).
     * @param array $parameterIDs IDs of the parameters belonging to this fieldset.
     * 
     */
    public function addFieldset($label, array $parameterIDs)
    {
        foreach ($parameterIDs as $parameterID)
        {
            if (array_key_exists($parameterID, $this->parameters))
            {
                $parameter = $this->parameters[$parameterID];
                $this->fieldsets[$label][] = $parameter;
                unset($this->parameters[$parameterID]);
            }
        }
    }
    
    /**
     * Set error messages for all parameters.
     * 
    // ---------------------------------------------------------------
     * This method ignores the MessageInterface instance if it is not
     * attached to any of the parameter ID of this form.
     * 
     * @param array $messages Contains MessageInterface implementations.
     * 
     */
    public function setParameterErrorMessages(array $messages)
    {
        foreach ($messages as $message)
        {
            if (!is_object($message) OR !($message instanceof MessageInterface))
            {
                continue;
            }
            
            $parameterID = $message->getParameterID();
            
            if ($parameterID != FALSE and array_key_exists($parameterID, $this->parameters))
            {
                $message->setParameterLabels($this->labels);
                $parameter = $this->parameters[$parameterID];
                $parameter->addErrorMessage($message->get());
            }
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
        $requestArray = $this->getRequestArray($request);
        
        foreach ($this->parameters as $parameter)
        {
            $parameter->getField()
                      ->setDefaultValue($requestArray);
        }
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    public function getSubmissionValue()
    {
        
    }
    
    /**
     * Get the list of parameter labels
     *
     */
    public function getParameterLabels()
    {
        return $this->labels;
    }
    
    /**
     * Gets fieldsets, along with Parameter instances that belong the them.
     * 
     * 
     * 
     */
    public function getFieldsets()
    {
        return $this->fieldsets;
    }
    
    /**
     * Get instances of Parameter not attached to any fieldset.
     * 
     * 
     * 
     */
    public function getParameters()
    {
        return $this->parameters;
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
    protected function getRequestArray(Request $request)
    {
        if ($this->method == 'post')
        {
            return $request->getPost();
        }
        
        return $request->getGet();
    }
}