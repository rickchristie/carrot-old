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
 * Checkbox Field
 * 
 * Represents a single checkbox field, renders into a single HTML
 * checkbox input tag. Returns a boolean value, TRUE if it is
 * checked, FALSE otherwise.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

use Carrot\Message\ValidationErrorMessageInterface;

class CheckboxField implements FieldInterface
{
    /**
     * @var string The ID of this field.
     */
    protected $id;
    
    /**
     * @var string The label of this field.
     */
    protected $label;
    
    /**
     * @var Attributes Used to contain non-essential attributes.
     */
    protected $attributes;
    
    /**
     * @var array List of forbidden attribute names {@see Attributes}.
     */
    protected $forbidden = array('name', 'type', 'value', 'id', 'checked');
    
    /**
     * @var string Default value for this field.
     */
    protected $defaultValue = FALSE;
    
    /**
     * @var array Contains instances of ValidationErrorMessageInterface, represents error messages of this field.
     */
    protected $errorMessages = array();
    
    /**
     * Constructor.
     * 
     * Example construction:
     *
     * <code>
     * $checkbox = new CheckboxField(
     *     'agreed',
     *     'I have read and agreed to the terms of agreement',
     *     array(
     *         'class' => 'singleCheckbox'
     *     )
     * );
     * </code>
     * 
     * @param string $id The ID of this field.
     * @param string $label The label of this field.
     * @param array $attributes Array of non-essential attributes.
     * 
     */
    public function __construct($id, $label, array $attributes = array())
    {
        $this->id = $id;
        $this->label = $label;
        $this->attributes = new Attributes($attributes, $this->forbidden);
    }
    
    /**
     * Get the Attributes instance.
     * 
     * @return Attributes
     *
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Get the field value from the form submission arrays.
     * 
     * @see FieldInterface::getValue()
     * @param array $formSubmissionArray Could be either $_GET or $_POST.
     * @param array $fileSubmissionArray The $_FILES array.
     * @return bool The value from the submission arrays, or a default value. 
     * 
     */
    public function getValue(array $formSubmissionArray, array $fileSubmissionArray)
    {
        if (array_key_exists($this->id, $formSubmissionArray) AND $formSubmissionArray[$this->id] == '1')
        {
            return TRUE;
        }
        
        return $this->defaultValue;
    }
    
    /**
     * Checks if the form submission is valid.
     * 
     * If the array key doesn't exist in the form submission array, it
     * is assumed that the client does not check the checkbox.
     * Otherwise this method will check if the value is '1', as
     * rendered by {@see render()}.
     * 
     * @see FieldInterface::isSubmissionValid()
     * @param array $formSubmissionArray Could be either $_GET or $_POST.
     * @param array $fileSubmissionArray The $_FILE array.
     * @return bool TRUE if the submission array is valid, FALSE otherwise.
     * 
     */
    public function isSubmissionValid(array $formSubmissionArray, array $fileSubmissionArray)
    {
        if (!array_key_exists($this->id, $formSubmissionArray) OR $formSubmissionArray[$this->id] == '1')
        {
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * Set the default value for this field.
     * 
     * The default value is cast to boolean before it is set.
     * 
     * @see FieldInterface::setDefaultValue()
     * @param bool $defaultValue The default value, either TRUE or FALSE.
     * @return bool TRUE if default value set, FALSE if default value not set/invalid.
     * 
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = (bool) $defaultValue;
        return TRUE;
    }
    
    /**
     * Get the field ID string.
     * 
     * @see FieldInterface::getID()
     * @return string The field ID.
     *
     */
    public function getID()
    {
        return $this->id;
    }
    
    /**
     * Get field label string.
     * 
     * @see FieldInterface::getLabel()
     * @return string The field label.
     *
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * Get the HTML ID to fill the 'for' attribute in the field label.
     * 
     * @see FieldInterface::getForAttributeInLabel()
     * @return string|NULL The HTML ID attribute or NULL.
     * 
     */
    public function getForAttributeInLabel()
    {
        return $this->id;
    }
    
    /**
     * Add a ValidationErrorMessageInterface instance.
     * 
     * @see FieldInterface::addErrorMessage()
     * @param ValidationErrorMessageInterface $message The error message to add.
     * 
     */
    public function addErrorMessage(ValidationErrorMessageInterface $message)
    {
        $this->errorMessages[] = $message;
    }
    
    /**
     * Get ValidationErrorMessageInterface instances in an array.
     * 
     * Should return an array with this structure:
     *
     * <code>
     * $errorMessages = array(
     *     $validationErrorMessageA,
     *     $validationErrorMessageB,
     *     $validationErrorMessageC
     * );
     * </code>
     * 
     * @see FieldInterface::getErrorMessages()
     * @return array Contains ValidationErrorMessageInterface instances.
     * 
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }
    
    /**
     * Check if the field currently has error messages attached to it.
     *
     * This method is useful for renderers if it needs to render
     * fields with error messages differently.
     * 
     * @return bool TRUE if has error messages, FALSE otherwise.
     *
     */
    public function hasErrorMessages()
    {
        return (!empty($this->errorMessages));
    }
    
    /**
     * Returns TRUE if the field label should be rendered by the renderer.
     * 
     * For the single checkbox, the label of the field is included in
     * the field control itself. Thus there is no need to render the
     * label.
     * 
     * @see FieldInterface::shouldRendererRenderLabel()
     * @return bool TRUE if field label should be rendered, FALSE otherwise.
     * 
     */
    public function shouldRendererRenderLabel()
    {
        return FALSE;
    }
    
    /**
     * Returns TRUE if field errors should be rendered by the renderer.
     * 
     * @see FieldInterface::shouldRendererRenderErrors()
     * @return bool TRUE if the field errors should be rendered, FALSE otherwise.
     * 
     */
    public function shouldRendererRenderErrors()
    {
        return TRUE;
    }
    
    /**
     * Render the field control into HTML form string.
     * 
     * @see FieldInterface::render()
     * @return string The field control, rendered as a HTML string, properly escaped.
     * 
     */
    public function render()
    {
        $id = htmlentities($this->id, ENT_QUOTES);
        $attributes = $this->attributes->render();
        $checked = '';
        
        if ($this->defaultValue)
        {
            $checked = ' checked="checked"';
        }
        
        return "<input type=\"checkbox\" name=\"{$id}\" id=\"{$id}\"{$checked}{$attributes} />";
    }
}