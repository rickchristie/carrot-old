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
 * Radio Group Field
 * 
// ---------------------------------------------------------------
 * Value object
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

use Carrot\Message\ValidationErrorMessageInterface;

class RadioGroupField implements FieldInterface
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
     * 
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
     * Get the field value from the form submission arrays.
     * 
     * @see FieldInterface::getValue()
     * @param array $formSubmissionArray Could be either $_GET or $_POST.
     * @param array $fileSubmissionArray The $_FILES array.
     * @return mixed The value from the submission arrays, or a default value. 
     * 
     */
    public function getValue(array $formSubmissionArray, array $fileSubmissionArray)
    {
        
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
        
    }
    
    /**
     * Set the default value for this field.
     * 
     * @see FieldInterface::setDefaultValue()
     * @param mixed $defaultValue The default value.
     * @return bool TRUE if default value set, FALSE if default value not set/invalid.
     * 
     */
    public function setDefaultValue($defaultValue)
    {
        
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
     * @see FieldInterface::shouldRendererRenderLabel()
     * @return bool TRUE if field label should be rendered, FALSE otherwise.
     * 
     */
    public function shouldRendererRenderLabel()
    {
        
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
        
    }
}