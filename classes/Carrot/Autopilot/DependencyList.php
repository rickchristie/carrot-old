<?php

namespace Carrot\Autopilot;

use InvalidArgumentException;

/**
 * Represents the list of object dependencies that must be
 * satisfied.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class DependencyList
{
    /**
     * @see getList()
     * @var array $list
     */
    private $list;
    
    /**
     * @see setObject()
     * @var array $objects
     */
    private $objects = array();
    
    /**
     * Constructor.
     * 
     * @param array $list
     *
     */
    public function __construct(array $list = array())
    {
        foreach ($list as $identifier)
        {
            $this->add($identifier);
        }
    }
    
    /**
     * Add the given identifier to the list. 
     * 
     * @param Identifier $identifier
     *
     */
    public function add(Identifier $identifier)
    {
        $string = $identifier->get();
        $this->list[$string] = $identifier;
    }
    
    /**
     * Get the list of dependencies in the form of Identifier
     * instances.
     * 
     * @return array
     *
     */
    public function getList()
    {
        return $this->list;
    }
    
    /**
     * Fill the dependency by giving the instantiated object that
     * belongs to the identifier string.
     * 
     * @param string $identifierString
     * @param mixed $object
     *
     */
    public function setObject($identifierString, $object)
    {   
        if (array_key_exists($identifierString, $this->list) == FALSE)
        {
            throw new InvalidArgumentException("Dependency list does not contain {$identifierString}.");
        }
        
        $identifier = $this->list[$identifierString];
        
        if ($identifier->checkClass($object))
        {
            $this->objects[$identifierString] = $object;
        }
    }
    
    /**
     * Get the instantiated dependency with the given identifier
     * string. NULL is returned if the dependency is not set.
     * 
     * @param string $identifierString
     * @return mixed|NULL
     *
     */
    public function getObject($identifierString)
    {
        if (array_key_exists($identifierString, $this->objects) == FALSE)
        {
            throw new InvalidArgumentException("Error: Either dependency list does not contain {$identifierString} or it hasn't been set yet.");
        }
        
        return $this->objects[$identifierString];
    }
    
    /**
     * Returns TRUE if all dependencies have been instantiated and
     * set in this dependency list.
     * 
     * @return bool
     *
     */
    public function isFulfilled()
    {
        return (count($this->list) == count($this->objects));
    }
}