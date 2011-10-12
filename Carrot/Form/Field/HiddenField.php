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
 * Hidden Field
 * 
 * Represents a hidden text field, renders into a single HTML
 * hidden input tag. Returns a string value.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

use Carrot\Message\ValidationErrorMessageInterface;

class HiddenField implements FieldInterface
{
    /**
     * @var string The ID of this field.
     */
    protected $id;
    
    /**
     * @var Attributes Used to contain non-essential attributes.
     */
    protected $attributes;
    
    /**
     * @var array List of forbidden attribute names {@see Attributes}.
     */
    protected $forbidden = array('name', 'type', 'value', 'id');
    
    /**
     * @var string Default value for this field.
     */
    protected $defaultValue = '';
    
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
     * $hidden = new HiddenField(
     *     'token',
     *     'iausdhg9y34908YA)*Y)(Bgfwoieuhassa223'
     * );
     * </code>
     * 
     * @param string $id The ID of this field.
     * @param string $defaultValue The default value for this field.
     * @param array $attributes Array of non-essential attributes.
     * 
     */
    public function __construct($id, $defaultValue, array $attributes = array())
    {
        $this->id = $id;
        $this->defaultValue = $defaultValue;
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
     * @return string The value from the submission arrays, or a default value. 
     * 
     */
    public function getValue(array $formSubmissionArray, array $fileSubmissionArray)
    {
        if ($this->isSubmissionValid($formSubmissionArray, $fileSubmissionArray))
        {
            return $formSubmissionArray[$this->id];
        }
        
        return $this->defaultValue;
    }
    
    /**
     * Checks if the form submission is valid.
     * 
     * @see FieldInterface::isSubmissionValid()
     * @param array $formSubmissionArray Could be either $_GET or $_POST.
     * @param array $fileSubmissionArray The $_FILE array.
     * @return bool TRUE if the submission array is valid, FALSE otherwise.
     * 
     */
    public function isSubmissionValid(array $formSubmissionArray, array $fileSubmissionArray)
    {
        return (
            array_key_exists($this->id, $formSubmissionArray) AND
            is_string($formSubmissionArray[$this->id])
        );
    }
    
    /**
     * Set the default value for this field.
     * 
     * The default value is cast to string.
     * 
     * @see FieldInterface::setDefaultValue()
     * @param string $defaultValue The default value.
     * @return bool TRUE if default value set, FALSE if default value not set/invalid.
     * 
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = (string) $defaultValue;
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
     * Hidden field doesn't have a label string.
     * 
     * @see FieldInterface::getLabel()
     * @return string Empty string.
     *
     */
    public function getLabel()
    {
        return '';
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
     * Hidden field doesn't have a label.
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
     * Strictly speaking, a hidden field wouldn't have any error
     * message.
     * 
     * @see FieldInterface::shouldRendererRenderErrors()
     * @return bool TRUE if the field errors should be rendered, FALSE otherwise.
     * 
     */
    public function shouldRendererRenderErrors()
    {
        return FALSE;
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
        $value = htmlentities($this->defaultValue, ENT_QUOTES);
        $attributes = $this->attributes->render();
        return "<input type=\"hidden\" name=\"{$id}\" id=\"{$id}\" value=\"{$value}\"{$attributes} />";
    }
}