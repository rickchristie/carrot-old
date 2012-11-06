<?php

namespace Carrot\Autopilot;

use InvalidArgumentException;

/**
 * Value object. Represents an identifier to an Autopilot created
 * object. This class validates itself.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Identifier
{
    /**
     * @see get()
     * @var string $identifier
     */
    private $identifier;
    
    /**
     * @see getNamespace()
     * @var string $namespace
     */
    private $namespace;
    
    /**
     * @see getClass()
     * @var string $class
     */
    private $class;
    
    /**
     * @see getClassName()
     * @var string $className
     */
    private $className;
    
    /**
     * @see getName()
     * @var string $name
     */
    private $name;
    
    /**
     * Constructor.
     * 
     * @param string $identifier
     *
     */
    public function __construct($identifier)
    {
        $this->identifier = trim($identifier, '\\');
        $this->initialize();
    }
    
    /**
     * Get the complete identifier string.
     * 
     * @return string
     *
     */
    public function get()
    {
        return $this->identifier;
    }
    
    /**
     * Get the class name (complete with namespace) of this
     * identifier (without backslash prefix).
     * 
     * @return string
     *
     */
    public function getClass()
    {
        return $this->class;
    }
    
    /**
     * Get only the class name (without namespace) of this identifier.
     * 
     * @return string
     *
     */
    public function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Get the namespace of this identifier (without backslash
     * suffix). If the namespace is root, an empty string will be
     * returned instead.
     * 
     * @return string
     *
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
    
    /**
     * Get the name of this identifier.
     * 
     * @return string
     *
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns TRUE if the class of this identifier is a child of
     * this namespace.
     * 
     * @param string $namespace
     * @return bool
     *
     */
    public function isInNamespace($namespace)
    {
        $namespace = trim($namespace, '\\');
        $namespaceLength = strlen($namespace);
        $identifierNamespaceLength = strlen($this->namespace);
        
        if ($namespaceLength > $identifierNamespaceLength)
        {
            return FALSE;
        }
        
        $cutIdentifierNamespace = substr($this->namespace, 0, $namespaceLength);
        return ($namespace == $cutIdentifierNamespace);
    }
    
    /**
     * Returns TRUE if the namespace of the class match the given
     * namespace string.
     * 
     * @param string $namespace
     * @return bool
     *
     */
    public function isNamespace($namespace)
    {
        $namespace = trim($namespace, '\\');
        return ($this->namespace == $namespace);
    }
    
    /**
     * Slices the identifier string to various parts.
     * 
     * @see __construct()
     *
     */
    private function initialize()
    {
        $pattern = '/^(([a-zA-Z0-9_\x7f-\xff\\\\]+\\\\)*([a-zA-Z0-9_\x7f-\xff]+))@([A-Za-z0-9_.]+)$/uD';
        $result = preg_match($pattern, $this->identifier, $matches);
        
        if ($result == FALSE)
        {
            throw new InvalidArgumentException("Invalid Autopilot Identifier: {$this->identifier}.");
        }
        
        $this->class = trim($matches[1], '\\');
        $this->namespace = trim($matches[2], '\\');
        $this->className = $matches[3];
        $this->name = $matches[4];
    }
}