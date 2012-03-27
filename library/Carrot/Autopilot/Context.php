<?php

namespace Carrot\Autopilot;

use InvalidArgumentException;

/**
 * Represents that contexts used in rulebooks.
 * 
 * Several of Autopilot's rulebook requires the usage of dynamic
 * context strings, which tells the namespace or class in which
 * the rule takes place. This object represents them.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Context
{
    /**
     * The regular expression used to parse the context string.
     * 
     * @var string $regex
     */
    private $regex = '/^(Namespace|Class):([A-Za-z_\\\\0-9]+)(\\*)?$/';
    
    /**
     * The class name or the namespace, depending on the context type,
     * without backslash prefix and suffix.
     * 
     * @var string $context
     */
    private $context;
    
    /**
     * If TRUE, then this context applies to everything.
     * 
     * @var bool $isWildcard
     */
    private $isWildcard;
    
    /**
     * If TRUE, then this context applies to a namespace, otherwise
     * this context applies to a class.
     * 
     * @var bool $isNamespace
     */
    private $isNamespace;
    
    /**
     * If TRUE, then this context applies to every class children
     * or all members of the provided namespace.
     * 
     * @var bool $isIncludingChildren
     */
    private $isIncludingChildren;
    
    /**
     * Constructor.
     * 
     * To define a context that includes only a class:
     * 
     * <pre>
     * Class:Carrot\MySQLi\MySQLi
     * </pre>
     * 
     * To define a context that includes a class and all its
     * children:
     * 
     * <pre>
     * Class:Carrot\MySQLi\MySQLi*
     * </pre>
     * 
     * To define a context that includes only direct members of a
     * namespace:
     * 
     * <pre>
     * Namespace:Carrot\MySQLi
     * </pre>
     * 
     * To define a context that includes all members of the
     * namespace:
     * 
     * <pre>
     * Namespace:Carrot\MySQLi*
     * </pre>
     * 
     * To define a context that includes everything:
     * 
     * <pre>
     * *
     * </pre>
     * 
     * @param string $string
     *
     */
    public function __construct($string)
    {
        $this->string = $string;
        
        if ($string == '*')
        {
            $this->isWildcard = TRUE;
            return;
        }
        
        $result = preg_match_all($this->regex, $string, $matches);
        
        if ($result <= 0)
        {
            throw new InvalidArgumentException("The context string '{$string}' is not valid.");
        }
        
        $this->isWildcard = FALSE;
        $this->context = trim($matches[2][0], '\\');
        $this->isIncludingChildren = ($matches[3][0] == '*');
        $this->isNamespace = ($matches[1][0] == 'Namespace');
    }
    
    /**
     * Checks if this context includes the given Autopilot reference.
     * 
     * If the context is a class, and the class doesn't exist, then
     * this method will immediately return FALSE.
     * 
     * @throws RuntimeException 
     * @param Reference $reference
     * @return bool
     *
     */
    public function includes(Reference $reference)
    {
        $class = $reference->getClassName();
        $contextLength = strlen($this->context);
        $classFirstPart = substr($class, 0, $contextLength);
        $classRemainingPart = substr($class, $contextLength);
        
        if ($this->isWildcard)
        {
            return TRUE;
        }
        
        if ($this->isNamespace)
        {
            if ($this->isIncludingChildren)
            {
                return (
                    $this->context == $classFirstPart AND
                    preg_match('/^\\\\/', $classRemainingPart) > 0
                );
            }
            else
            {
                return (
                    $this->context == $classFirstPart AND
                    preg_match('/^\\\\[^\\\\]+$/', $classRemainingPart) > 0
                );
            }
        }
        else
        {
            if (
                class_exists($this->context) == FALSE OR
                class_exists($class) == FALSE
            )
            {
                return FALSE;
            }
            
            if ($this->isIncludingChildren)
            {   
                return (
                    $this->context == $class OR
                    is_subclass_of($class, $this->context)
                );
            }
            else
            {
                return ($this->context == $class);
            }
        }
        
        return FALSE;
    }
    
    /**
     * Get the string that represents the context.
     * 
     * The context string acts like an ID. If two Context instance
     * has the same context string, both instance represents the
     * same context.
     * 
     * @return string
     *
     */
    public function getString()
    {
        return $this->string;
    }
    
    /**
     * Checks if this context is a wildcard context.
     * 
     * @return bool
     *
     */
    public function isWildcard()
    {
        return $this->isWildcard;
    }
    
    /**
     * Checks if this context is a class type context that doesn't
     * includes its children.
     * 
     * @return bool
     *
     */
    public function isClass()
    {
        return ($this->isNamespace === FALSE AND $this->isIncludingChildren === FALSE);
    }
    
    /**
     * Checks if this context is a class type context that includes
     * its child classes.
     * 
     * @return bool
     *
     */
    public function isGreedyClass()
    {
        return ($this->isNamespace === FALSE AND $this->isIncludingChildren === TRUE);
    }
    
    /**
     * Checks if this context is a namespace type context that only
     * includes direct members.
     * 
     * @return bool
     *
     */
    public function isNamespace()
    {
        return ($this->isNamespace === TRUE AND $this->isIncludingChildren === FALSE);
    }
    
    /**
     * Checks if this context is a namespace type context that
     * includes all its members, regardless of the depth.
     * 
     * @return bool
     *
     */
    public function isGreedyNamespace()
    {
        return ($this->isNamespace === TRUE AND $this->isIncludingChildren === TRUE);
    }
    
    /**
    //---------------------------------------------------------------
     * 
     * 
     * @return Context The one with 
     *
     */
    public function clash(Context $context)
    {
        
    }
}