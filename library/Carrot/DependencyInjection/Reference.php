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
 * Reference to a specific object instance in Carrot.
 *
 * This class is a container and validator for an instance ID,
 * which is a string that consists of concatenated fully
 * qualified class name, lifecycle setting, and instance name
 * (if any). This object, since it contains an instance ID,
 * is used to 'refer' to a specific object instance wired by the
 * dependency injector container. This is a value object.
 * 
 * This object is used by the container to pinpoint which
 * instance to be instantiated. Likewise, each dependency
 * configuration must also be saved in a way that connects them
 * a Reference object/instance ID.
 *
 * Please note that by default, the instance name is left empty.
 * A Reference object without an instance name is often referred
 * to as 'unnamed reference'.
 * 
 * You should only provide an instance name when it is necessary,
 * that is, if there is going to be another object in your
 * application with different instantiation configuration. For
 * example, if your application uses only one database object,
 * you don't need to name it, simply save its injection
 * configuration to an unnamed reference.
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\DependencyInjection;

use InvalidArgumentException;

class Reference
{   
    /**
     * @var array The list of allowed lifecycle values.
     */
    protected $allowedLifecycle = array(
        'Singleton',
        'Transient'
    );
    
    /**
     * @var string Fully qualified class name of the instance this object is referring to.
     */
    protected $class;
    
    /**
     * @var string Lifecycle setting of the instance this object is referring to.
     */
    protected $lifecycle;
    
    /**
     * @var string The name of the instance this object is referring to.
     */
    protected $name;
    
    /**
     * @var string The instance ID string.
     */
    protected $id;
    
    /**
     * Constructor.
     *
     * @param string $class Fully qualified class name of the
     *        instance this object is referring to.
     * @param string $name The instance name of the instance this
     *        object is referring to.
     * @param string $lifecycle Lifecycle setting of the instance
     *        this object is referring to. Could be either
     *        'Singleton' or 'Transient', case sensitive.
     *
     */
    public function __construct($class, $lifecycle = 'Singleton', $name = '')
    {
        $this->class = trim($class, '\\');
        $this->name = $name;
        
        if (!in_array($lifecycle, $this->allowedLifecycle))
        {
            throw new InvalidArgumentException("Reference error in instantiation. Lifecycle '{$lifecycle}' is not recognized. It must be either 'Singleton', or 'Transient', case sensitive.");
        }
        
        $this->lifecycle = $lifecycle;
        $this->id = "{$this->class}{{$this->lifecycle}:{$this->name}}";
    }
    
    /**
     * Get the fully qualified class name of the instance this object
     * is referring to.
     *
     * @return string
     *
     */
    public function getClass()
    {
        return $this->class;
    }
    
    /**
     * Get the name of the instance this object is referring to.
     *
     * @return string
     *
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Get the instance ID that this reference object refers to.
     *
     * The instance ID is a string concatenated from the fully
     * qualified class name, the lifecycle setting and the instance
     * name (if any).
     *
     * The syntax for instance ID, in extended BNF format:
     *
     * <code>
     * instance id = fully qualified class name , "{" ,
     *               lifecycle setting , ":" , [instance name] , "}"
     * lifecycle setting = "Singleton" | "Transient"
     * </code>
     *
     * Example generated instance ID string:
     *
     * <code>
     * Carrot\Request{Singleton:}
     * Carrot\Database\MySQLiWrapper\MySQLi{Singleton:Main}
     * Carrot\Database\MySQLiWrapper\MySQLi{Singleton:Backup}
     * </code>
     * 
     * The instance ID is meant to be unique. This means if two
     * Reference object instance generates the same instance ID
     * string, they are referring to the same object instance.
     *
     * @return string
     *
     */
    public function getID()
    {
        return $this->id;
    }
    
    /**
     * Checks if the instance this object is referring to has a
     * singleton lifecycle.
     *
     * @return bool TRUE if the lifecycle is singleton, FALSE
     *         otherwise.
     *
     */
    public function isSingleton()
    {
        return ($this->lifecycle == 'Singleton');
    }
    
    /**
     * Checks if the instance this object is referring to has a
     * transient lifecycle.
     *
     * @return bool TRUE if the lifecycle is transient, FALSE
     *         otherwise.
     *
     */
    public function isTransient()
    {
        return ($this->lifecycle == 'Transient');
    }
}