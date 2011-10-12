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
 * Attributes
 * 
 * Value object, represents HTML attributes. Used by some of the
 * default FieldInterface implementations to note the attributes
 * owned by their form controls.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

use RuntimeException;
use InvalidArgumentException;

class Attributes
{
    /**
     * @var array The attributes array, with the name of the attribute as index.
     */
    protected $attributes;
    
    /**
     * @var array List of attribute names that must not be set.
     */
    protected $forbidden;
    
    /**
     * Constructor.
     * 
     * Pass the initial attributes array and the list of forbidden
     * attributes to the constructor:
     *
     * <code>
     * $attributes = new Attributes(
     *     array(
     *         'maxlength' => '10',
     *         'class' => 'textField'
     *     ),
     *     array(
     *         'value',
     *         'id',
     *         'name'
     *     )
     * );
     * </code>
     * 
     * Throws InvalidArgumentException if one of the attributes given
     * is forbidden.
     * 
     * @throws InvalidArgumentException
     * @param array $attributes The attributes array.
     * @param array $forbidden The list of forbidden attribute names.
     * 
     */
    public function __construct(array $attributes = array(), array $forbidden = array())
    {
        foreach ($attributes as $name => $value)
        {
            if (in_array($name, $forbidden))
            {
                throw new InvalidArgumentException("Attributes error in instantiation. The attribute '{$name}' is forbidden.");
            }
        }
        
        $this->attributes = $attributes;
        $this->forbidden = $forbidden;
    }
    
    /**
     * Set an attribute.
     * 
     * Set an attribute value.
     * 
     * <code>
     * $attributes->set('maxlength', '10');
     * </code>
     * 
     * Throws InvalidArgumentException if the attribute name belongs
     * to the forbidden attributes.
     * 
     * @throws InvalidArgumentException
     * @param string $name The name of the attribute to set.
     * @param string $value The value to set it to.
     * 
     */
    public function set($name, $value)
    {
        if (in_array($nam, $this->forbidden))
        {
            throw new InvalidArgumentException("Attributes error when attempting to set value. The attribute '{$name}' is forbidden.");
        }
        
        $this->attributes[$name] = $value;
    }
    
    /**
     * Set an attribute only if it does not exist, hence setting default value.
     * 
     * Will not set the attribute if the attribute already exists.
     * 
     * @param string $name The name of the attribute to set.
     * @param string $value The value to set it to.
     * 
     */
    public function setDefault($name, $value)
    {
        if (!array_key_exists($name, $this->attributes))
        {
            $this->set($name, $value);
        }
    }
    
    /**
     * Append a value to an attribute.
     * 
     * Very useful when, for example, you wanted to add a class to the
     * element but does not want to remove previously set classes.
     * 
     * <code>
     * $attributes->set('class', 'textField');
     * $attributes->append('class', 'highlighted');
     * </code>
     * 
     * @param string $name The name of the attribute to append.
     * @param string $value The value to append it to.
     * 
     */
    public function append($name, $value)
    {
        if (in_array($nam, $this->forbidden))
        {
            throw new RuntimeException("Attributes error when attempting to append value. The attribute '{$name}' is forbidden. Please use class specific methods.");
        }
        
        if (array_key_exists($name, $this->attributes))
        {
            $this->attributes[$name] = $value;
        }
        
        $this->attributes[$name] .= $value;
    }
    
    /**
     * Get a value of an attribute.
     * 
     * Will return NULL if the attribute does not exist.
     * 
     * @param string $name The name of the attribute to get.
     * @return string|NULL Returns NULL if the attribute does not exist.
     * 
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->attributes))
        {
            return NULL;
        }
        
        return $this->attributes[$name];
    }
    
    /**
     * Get a value of an attribute, or return a default value if it doesn't exist.
     * 
     * @param string $name The name of the attribute to get.
     * @param mixed $default The default value to be returned.
     * 
     */
    public function getOrReturnDefault($name, $default)
    {
        if (!array_key_exists($name, $this->attributes))
        {
            return $default;
        }
        
        return $this->attributes[$name];
    }
    
    /**
     * Returns all attributes in an array.
     *
     * @return array All attributes in an associative array.
     *
     */
    public function getAll()
    {
        return $this->attributes;
    }
    
    /**
     * Removes an attribute.
     *
     * @param string $name The name of the attribute to remove.
     *
     */
    public function remove($name)
    {
        unset($this->attributes[$name]);
    }
    
    /**
     * Checks if an attribute exists or not.
     *
     * @param string $name The name of the attribute to check.
     * @return bool TRUE if exists, FALSE otherwise.
     *
     */
    public function exists($name)
    {
        return (array_key_exists($name, $this->attributes));
    }
    
    /**
     * Render the attributes out as string.
     * 
     * This is the default rendering method and is here to make it
     * easier for you to build your custom fields. Example of returned
     * string:
     * 
     * <code>
     * // There's always a space before the first attribute
     * $attributes = ' name="username" id="username_id"';
     * </code>
     *
     * Both the attribute names and values will be escaped using
     * htmlentities() with the ENT_QUOTES option.
     * 
     * @return string HTML attributes string cleaned with htmlentities.
     * 
     */
    public function render()
    {
        $renderedAttributes = '';
        
        foreach ($this->attributes as $name => $value)
        {
            $name = htmlentities($name, ENT_QUOTES);
            $value = htmlentities($name, ENT_QUOTES);
            $renderedAttributes .= " {$name}=\"{$value}\"";
        }
        
        return $renderedAttributes;
    }
}