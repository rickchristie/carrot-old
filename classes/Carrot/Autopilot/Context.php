<?php

namespace Carrot\Autopilot;

use InvalidArgumentException;

/**
 * Value object. Represents a context for Autopilot to use.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Context
{
    /**
     * @see __construct()
     * @var string $pattern
     */
    private $pattern = '/^(Namespace|Class|Identifier):([a-zA-Z0-9_\x7f-\xff\\\\]+)((\\*)|(@[A-Za-z0-9_.]+))*$/uD';
    
    /**
     * @see __construct()
     * @var string $contextString
     */
    private $contextString;
    
    /**
     * @see isWildcard()
     * @var bool $isWildcard
     */
    private $isWildcard = FALSE;
    
    /**
     * @see isNamespace()
     * @var bool $isNamespace
     */
    private $isNamespace = FALSE;
    
    /**
     * @see isIdentifier()
     * @var bool $isIdentifier
     */
    private $isIdentifier = FALSe;
    
    /**
     * @see isClass()
     * @var bool $isClass
     */
    private $isClass = FALSE;
    
    /**
     * @see isGreedy()
     * @var bool $isGreedy
     */
    private $isGreedy = FALSE;
    
    /**
     * @see getContent()
     * @var string|NULL $content
     */
    private $content;
    
    /**
     * Constructor.
     * 
     * @param string $contextString
     *
     */
    public function __construct($contextString)
    {
        $this->contextString = $contextString;
        
        if ($contextString == '*')
        {
            $this->isWildcard = TRUE;
            return;
        }
        
        $result = preg_match_all($this->pattern, $contextString, $matches);
        
        if ($result <= 0)
        {
            throw new InvalidArgumentException("Invalid Autopilot identifier string: {$contextString}.");
        }
        
        $type = $matches[1][0];
        $this->content = $matches[2][0];
        
        if ($type == 'Identifier')
        {
            $this->content .= $matches[5][0];
            
            if (
                empty($matches[5][0]) OR
                $matches[5][0]{0} != '@'
            )
            {
                throw new InvalidArgumentException("Invalid identifier in context: {$this->content}.");
            }
            
            $this->isIdentifier = TRUE;
            return;
        }
        
        $this->isGreedy = ($matches[4][0] == '*');
        
        if ($type == 'Namespace')
        {
            $this->isNamespace = TRUE;
            $this->content = trim($this->content, '\\');
            return;
        }
        
        if ($type == 'Class')
        {
            $this->isClass = TRUE;
            return;
        }
    }
    
    /**
     * Returns TRUE if this context is a wildcard type context.
     * 
     * @return bool
     *
     */
    public function isWildcard()
    {
        return $this->isWildcard;
    }
    
    /**
     * Returns TRUE if this context is an identifier type context.
     * 
     * @return bool
     *
     */
    public function isIdentifier()
    {
        return $this->isIdentifier;
    }
    
    /**
     * Checks if this context is a class type context.
     * 
     * @return bool
     *
     */
    public function isClass()
    {
        return $this->isClass;
    }
    
    /**
     * Checks if this context is a class type context that only
     * includes the class, and not its children.
     *
     */
    public function isNonGreedyClass()
    {
        return (
            $this->isClass AND
            $this->isGreedy == FALSE
        );
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
        return (
            $this->isClass AND
            $this->isGreedy
        );
    }
    
    /**
     * Checks if this context is a namespace type context.
     * 
     * @return bool
     *
     */
    public function isNamespace()
    {
        return $this->isNamespace;
    }
    
    /**
     * Checks if this context is a namespace type context that only
     * includes direct members.
     * 
     * @return bool
     *
     */
    public function isNonGreedyNamespace()
    {
        return (
            $this->isNamespace AND
            $this->isGreedy == FALSE
        );
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
        return (
            $this->isNamespace AND
            $this->isGreedy
        );
    }
    
    /**
     * Returns TRUE if this context is either class or namespace type
     * context and it is greedy.
     * 
     * @return bool
     *
     */
    public function isGreedy()
    {
        return $this->isGreedy;
    }
    
    /**
     * Get the content of the context, could be either a class name,
     * a namespace, or a complete identifier string. Returns NULL if
     * this is a wildcard context.
     * 
     * @return string|NULL
     *
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Get the full context string.
     * 
     * @return string
     *
     */
    public function get()
    {
        return $this->contextString;
    }
    
    /**
     * Returns TRUE if the identifier given is included in this
     * context.
     * 
     * @param Identifier $identifier
     *
     */
    public function includes(Identifier $identifier)
    {
        if ($this->isWildcard)
        {
            return TRUE;
        }
        
        if ($this->isNamespace)
        {
            $namespace = $identifier->getNamespace();
            
            if ($this->isGreedy)
            {
                $length = strlen($this->content);
                $namespaceCut = substr($namespace, 0, $length);
                return ($this->content == $namespaceCut);
            }
            
            return ($namespace == $this->content);
        }
        
        if ($this->isClass)
        {
            $class = $identifier->getClass();
            
            if ($this->isGreedy)
            {   
                if (
                    class_exists($class) == FALSE OR
                    class_exists($this->content) == FALSE
                )
                {
                    return FALSE;
                }
                
                return (
                    $this->content == $class OR
                    is_subclass_of($class, $this->content)
                );
            }
            
            return ($this->content == $class);
        }
        
        // Check for Identifier.
        $identString = $identifier->get();
        return ($this->content == $identString);
    }
}