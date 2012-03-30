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
    private $regex = '/^(Namespace|Class):([A-Za-z_\\\\0-9]+)(\\*)?(@([A-Za-z_0-9]+)?)?(:(Singleton|Transient))?$/';
    
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
     * If TRUE, then this context includes a configuration name that
     * must be checked against the reference.
     * 
     * @var bool $hasConfigurationName
     */
    private $hasConfigurationName;
    
    /**
     * Contains the configuration name to be checked against Autopilot
     * references if $hasConfigurationName is TRUE.
     * 
     * @var string $configurationName
     */
    private $configurationName;
    
    /**
     * If TRUE, then this context includes a lifecycle setting that
     * must be checked against the reference.
     * 
     * @var bool $hasLifecycleSetting
     */
    private $hasLifecycleSetting;
    
    /**
     * Contains the lifecycle setting to be checked against Autopilot
     * references if $lifecycleSetting is TRUE.
     * 
     * @var string $lifecycleSetting
     */
    private $lifecycleSetting;
    
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
        
        if ($this->isNamespace)
        {   
            $this->hasConfigurationName = FALSE;
            $this->hasLifecycleSetting = FALSE;
        }
        else
        {   
            $this->hasConfigurationName = (empty($matches[4][0]) == FALSE);
            $this->configurationName = $matches[5][0];
            $this->hasLifecycleSetting = (empty($matches[7][0]) == FALSE);
            $this->lifecycleSetting = $matches[7][0];
        }
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
     * Checks if this context is a class type context.
     * 
     * @return bool
     *
     */
    public function isClass()
    {
        return ($this->isNamespace === FALSE);
    }
    
    /**
     * Checks if this context is a class type context that only
     * includes the class, and not its children.
     *
     */
    public function isNonGreedyClass()
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
     * Checks if this context is a namespace type context.
     * 
     * @return bool
     *
     */
    public function isNamespace()
    {
        return ($this->isNamespace === TRUE);
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
     * Checks if this context has a configuration name rule to check.
     * 
     * @return bool
     *
     */
    public function hasConfigurationName()
    {
        return $this->hasConfigurationName;
    }
    
    /**
     * Checks if this context has a lifecycle setting to check.
     * 
     * @return bool
     *
     */
    public function hasLifecycleSetting()
    {
        return $this->hasLifecycleSetting;
    }
    
    /**
     * Checks if this context is an atomic one, meaning that it only
     * applies to one specific Autopilot reference.
     * 
     * Atomic contexts has the highest priority. An atomic context
     * is a non greey class typed context with both configuration
     * name and lifecycle setting declared.
     * 
     * @return bool
     *
     */
    public function isAtomic()
    {
        return (
            $this->isNonGreedyClass() AND
            $this->hasLifecycleSetting() AND
            $this->hasConfigurationName()
        );
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
     * Checks if this context includes the given Autopilot reference.
     * 
     * If the context given is a class typed context and the class
     * doesn't exist, then this method will immediately return FALSE.
     * 
     * @throws RuntimeException 
     * @param Reference $reference
     * @return bool
     *
     */
    public function includes(Reference $reference)
    {
        if ($this->isWildcard)
        {
            return TRUE;
        }
        
        if ($this->isNamespace)
        {
            return $this->namespaceContextIncludes($reference);
        }
        else
        {
            return $this->classContextIncludes($reference);
        }
        
        return FALSE;
    }
    
    /**
     * Check if this context is more specific than the given Context.
     * 
     * Specificity in contexts has this hierarchy (lowest to highest):
     * 
     * - Wildcard
     * - Greedy namespace
     * - Greedy namespace with deeper namespace level
     * - Namespace
     * - Greedy class
     * - Greedy class with lifecycle setting
     * - Greedy class with configuration name
     * - Greedy class with both lifecycle setting & configuration name
     * - Class
     * - Class with lifecycle setting
     * - Class with configuration name
     * - Class with both lifecycle setting & configuration name
     * - Atomic class (points to a single Autopilot reference only)
     * 
     * For two greedy namespaces, the deeper one wins (the ones that
     * has more namespace entries). However, no level comparison
     * is performed on other cases.
     * 
     * Note that this method does not check whether or not both
     * contexts points to a same class, it just checks whether this
     * context is more specific than another context based on the
     * said rules.
     * 
     * @param Context $context
     * @return bool
     *
     */
    public function isMoreSpecificThan(Context $context)
    {
        if ($context->isWildcard())
        {
            // Everything else is more specific than wildcard.
            return ($this->isWildcard() == FALSE);
        }
        
        if ($context->isGreedyNamespace())
        {
            if ($this->isGreedyNamespace() == FALSE)
            {
                // Anything other than wildcard and greedy namespace
                // is more specific than a greedy namespace.
                return ($this->isWildcard() == FALSE);
            }
            
            return ($this->getLevel() > $context->getLevel());
        }
        
        if ($context->isNonGreedyNamespace())
        {   
            return ($this->isClass());
        }
        
        if ($context->isGreedyClass())
        {
            if ($this->isClass() == FALSE)
            {
                return FALSE;
            }
            
            if ($this->isNonGreedyClass())
            {
                return TRUE;
            }
            
            if (
                $context->hasConfigurationName() AND
                $this->hasConfigurationName == FALSE
            )
            {
                return FALSE;
            }
            
            if (
                $this->hasConfigurationName AND
                $context->hasConfigurationName() == FALSE
            )
            {
                return TRUE;
            }
            
            if (
                $this->hasLifecycleSetting AND
                $context->hasLifecycleSetting() == FALSE
            )
            {
                return TRUE;
            }
            
            return FALSE;
        }
        
        if ($context->isNonGreedyClass())
        {
            if ($this->isNonGreedyClass() == FALSE)
            {
                return FALSE;
            }
            
            if (
                $context->hasConfigurationName() AND
                $this->hasConfigurationName == FALSE
            )
            {
                return FALSE;
            }
            
            if (
                $this->hasConfigurationName AND
                $context->hasConfigurationName() == FALSE
            )
            {
                return TRUE;
            }
            
            if (
                $this->hasLifecycleSetting AND
                $context->hasLifecycleSetting() == FALSE
            )
            {
                return TRUE;
            }
            
            // There is nothing more specific than an
            // atomic class.
            return FALSE;
        }
    }
    
    /**
     * Check if this namespace context includes the given Autopilot
     * reference.
     * 
     * @see includes()
     * @param Reference $reference
     * @return bool
     *
     */
    private function namespaceContextIncludes(Reference $reference)
    {
        $class = $reference->getClassName();
        $contextLength = strlen($this->context);
        $classFirstPart = substr($class, 0, $contextLength);
        $classRemainingPart = substr($class, $contextLength);
        
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
    
    /**
     * See if this class context includes the given Autopilot reference.
     * 
     * @see includes()
     * @param Reference $reference
     *
     */
    private function classContextIncludes(Reference $reference)
    {
        $class = $reference->getClassName();
        
        if (
            class_exists($this->context) == FALSE OR
            class_exists($class) == FALSE
        )
        {
            // If the class does not exist, we cannot check
            // for inheritance, so it's pointless anyway.
            return FALSE;
        }
        
        if (
            $this->hasConfigurationName() AND
            $reference->isConfigurationName($this->configurationName) == FALSE
        )
        {
            return FALSE;
        }
        
        if (
            $this->hasLifecycleSetting() AND
            $reference->isLifecycle($this->lifecycleSetting) == FALSE
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
}