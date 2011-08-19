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
 * Field Interface
 * 
// ---------------------------------------------------------------
 * Value object
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

interface FieldInterface
{
    /**
     * Get the request variable value from a request array.
     * 
     * The request array could be either GET or POST array, but the
     * field object does not need to know which. Since the field
     * object is the one responsible for rendering itself into HTML
     * form tags, it is also the only object that knows how to get the
     * parameter value from the request array.
     * 
    // ---------------------------------------------------------------
     * If the request variable value doesn't exist (e.g. the form
     * hasn't been submitted yet) this method will return NULL.
     * 
     * @param array $requestArray The form request variable array.
     * @return mixed|NULL If the 
     * 
     */
    public function getValue(array $formSubmissionArray);
    
    /**
     * Checks if the field can return request variable value from the given request array.
     * 
     * You must return TRUE if the request array contains all the
     * information you need to get request variable value, which means
     * {@see getRequestVariableValue} can be called safely. If you
     * can't, return FALSE and the form submission will be deemed as
     * invalid.
     *
    // ---------------------------------------------------------------
     * For most fields, this method will only check for the existence
     * of request variable name as an index of the request array. If
     * the form is submitted and everything is correct, the request
     * variable name must be present in the request array index.
     * 
     * 
    // ---------------------------------------------------------------
     * However, this is not the case for checkbox field, where request
     * variable can always be processed since if a checkbox field is
     * not checked, the value does not get sent to the server.
     * 
     * For a form submission to be valid, all form parameter's request
     * variable must be in the request array. However, each field may
     * store its variable in the request 
     * 
     * @return bool TRUE if the request array contains 
     * 
     */
    public function isSubmissionValid(array $formSubmissionArray);
    
    /**
     * Set the default value for this field.
     * 
     * 
     * 
     * @param array $requestArray
     * 
     */
    public function setDefaultValue($defaultValue);
    
    /**
     * Get field ID
     *
     */
    public function getID();
    
    /**
     * Get field label
     *
     */
    public function getLabel();
    
    /**
     * Add error message string
     *
     */
    public function addErrorMessage($message);
    
    /**
     * Get error messages
     *
     */
    public function getErrorMessages();
    
    /**
     * Render the field as HTML form string.
     * 
    // ---------------------------------------------------------------
     * This method is responsible of rendering the field object into
     * HTML form tags. When you implement this method, you must
     * remember to escape variables from outside properly.
     * 
     * @return string The field, rendered as a HTML string.
     * 
     */
    public function renderControl();
    
    public function renderLabel();
}