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
 * Destination
 * 
 * Value object, represents a Destination, namely, the controller's DIC item ID
 * (for instantiation), the method to call and the arguments to pass to the method.
 * Returned by Router class, used by the front controller.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class Destination
{
    /**
     * @var string Controller's DIC item ID.
     */
    protected $controller_dic_id;
    
    /**
     * @var string Method name to be called.
     */
    protected $method;
    
    /**
     * @var array Array of paramters to be passed.
     */
    protected $params;
    
    /**
     * @var string The name of the controller class derived from the DIC ID.
     */
    protected $class_name;
    
    /**
     * Creates a Destination object.
     *
     * Will throw an exception if the controller DIC item ID doesn't
     * pass validation process. Example usage:
     *
     * <code>
     * $destination = new Destination
     * (
     *     '\Vendor\Namespace\Subnamespace\BlogController@main',
     *     'index',
     *     array(5, 'Foo', 'Bar')
     * );
     * </code>
     *
     * @param string $controller_dic_id DIC item ID for the controller.
     * @param string $method Method name to call.
     * @param array $params Array of parameters, to be passed in sequence.
     *
     */
    public function __construct($controller_dic_id, $method, array $params = array())
    {
        if (!$this->validateID($controller_dic_id))
        {
            throw new \InvalidArgumentException("Error in creating Destination object, Controller DIC registration ID is not valid ({$controller_dic_id}).");
        }
        
        $this->controller_dic_id = $controller_dic_id;
        $this->method = $method;
        $this->params = $params;
        $this->class_name = $this->getClassNameFromID($controller_dic_id);
    }
    
    /**
     * Returns the controller DIC item registration ID.
     *
     * @return string Controller DIC item registration ID.
     *
     */
    public function getControllerDICItemID()
    {
        return $this->controller_dic_id;
    }
    
    /**
     * Returns the method to call from the controller.
     *
     * @return string Method name to call.
     *
     */
    public function getMethodName()
    {
        return $this->method;
    }
    
    /**
     * Returns parameters to pass to the method.
     *
     * @return array Parameters to be passed to the controller method, sequentially.
     *
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Returns class name.
     *
     * @return string The controller's class name, generated from the DIC item ID.
     *
     */
    public function getClassName()
    {
        return $this->class_name;
    }
    
    // ---------------------------------------------------------------
    
    /**
     * Validates DIC registration ID.
     *
     * The following rules must be satisfied:
     *
     *  1. Must use fully qualifed name (with starting backslash).
     *  2. Must have a configuration name after FQN, separated by '@'.
     *
     * @param string $dic_id DIC item ID.
     * @return bool True if valid, false otherwise.
     *
     */
    protected function validateID($dic_id)
    {
        $dic_id_exploded = explode('@', $dic_id);
        
        return
        (
            count($dic_id_exploded) == 2 &&
            !empty($dic_id_exploded[0]) &&
            !empty($dic_id_exploded[1]) &&
            $dic_id_exploded[0]{0} == '\\'
        );
    }
    
    /**
     * Get the fully qualified class name from DIC item ID.
     *
     * @param string $id DIC item ID.
     * @return string Fully qualified class name.
     *
     */
    protected function getClassNameFromID($id)
    {
        $id_exploded = explode('@', $id);
        return $id_exploded[0];
    }
}