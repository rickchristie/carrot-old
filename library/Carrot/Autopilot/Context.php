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
     * Use asterisk to define a context that includes everything:
     * 
     * <pre>
     * *
     * </pre>
     *
     * To define a context that includes only classes with the given
     * Autopilot configuration name:
     * 
     * <pre>
     * // This will apply to both instances with transient
     * // and singleton lifecycle setting.
     * Class:Carrot\MySQLi\MySQLi@Main
     * </pre>
     * 
     * To define a context that includes classes plus its childrens
     * with the given Autopilot configuration name:
     * 
     * <pre>
     * // This will apply to the class and its children
     * // that has the configuration name 'Database'.
     * Class:App\Logging\LoggerInterface*@Database
     * </pre>
     * 
     * To define a context that includes only the class with the
     * given Autopilot reference:
     * 
     * <pre>
     * // This will apply to only a specific Autopilot reference.
     * Class:Carrot\MySQLi\MySQLi@Main:Singleton
     * </pre>
     * 
     * The configuration name lifecycle setting in the context string
     * will be ignored if the type is not 'Class'.
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
     * Get the namespace level of this context.
     * 
     * By level, we mean the number of namespace in the context.
     * <code>Carrot\MySQLi</code> will be 1, while
     * <code>Carrot\MySQLi\MySQLi</code> will be 2.
     * 
     * @return int
     *
     */
    public function getLevel()
    {
        return preg_match_all('/\\\\/', $this->context, $matches);
    }
    
    /**
     * Check if this context is more specific than the given Context.
     * 
     * Specificity in contexts has this hierarchy (lowest to highest):
     * 
     * - Wildcard
     * - Greedy namespace
     * - Namespace
     * - Greedy class
     * - Class
     * 
     * For two greedy namespaces, the deeper one wins (the ones that
     * has more namespace entries). However, no level comparison
     * is performed on other cases.
     * 
     * Note that this method does not check whether or not both
     * contexts points to the same class, it just checks whether this
     * context is more specific than another context based on the
     * above rules.
     * 
     * @param Context $context
     * @return bool
     *
     */
    public function isMoreSpecificThan(Context $context)
    {
        if ($context->isWildcard())
        {
            return ($this->isWildcard() == FALSE);
        }
        
        if ($context->isGreedyNamespace())
        {
            return $this->isMoreSpecificThanGreedyNamespace($context);
        }
        
        if ($context->isNamespace())
        {
            return (
                $this->isGreedyClass() OR
                $this->isClass()
            );
        }
        
        if ($context->isGreedyClass())
        {   
            return $this->isClass();
        }
        
        if ($context->isClass())
        {
            // There is nothing more specific than a
            // class typed context.
            return FALSE;
        }
    }
    
    /**
     * Check if this context is more specific than the provided
     * greedy namespace context.
     * 
     * We count the number of backslashes in the context to
     * determine which is more specific if both contexts are
     * greedy namespace type.
     * 
     * @param Context $context
     * @return bool
     *
     */
    private function isMoreSpecificThanGreedyNamespace(Context $context)
    {
        if ($this->isGreedyNamespace() == FALSE)
        {
            return ($this->isWildcard() == FALSE);
        }
        
        return ($this->getLevel() > $context->getLevel());
    }
}