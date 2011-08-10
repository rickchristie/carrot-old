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
 * Basic Route
 * 
// ---------------------------------------------------------------
 * This class is the basic route class
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */


namespace Carrot\Core;

use InvalidArgumentException;
use Carrot\Core\Interfaces\RouteInterface;

class BasicRoute implements RouteInterface
{
    /**
     * @var string The route registration ID for this route object.
     */
    protected $id;
    
    /**
     * @var string The pattern of this route.
     */
    protected $pattern;
    
    /**
     * @var type comments
     */
    protected $routineObjectInstanceName;
    
    protected $routineMethodName;
    
    protected $requestType;
    
    /**
     * @var type comments
     */
    protected $placeholderPrefix;
    
    protected $variablePrefix;
    
    protected $variableSuffix;
    
    protected $routineMethodArgs;
    
    protected $variableNamePattern;
    
    /**
     * Constructor.
     * 
     * Here is an example of a full configuration value construction:
     *
     * <code>
     * $route = new BasicRoute('RouteID', array(
     *     'pattern' => '/blog/{id}/{slug}/',
     *     'object' => 'App\Blog\PostController{Main:Transient}',
     *     'method' => 'showPost',
     *     'prefix' => '@',
     *     'args' => array('@id', '@slug'),
     *     'type' => 'GET',
     *     'varPrefix' => '{',
     *     'varSuffix' => '}'
     * ), $request, $appRequestURI);
     * </code>
     * 
     * The pattern is matched against AppRequestURI::getPathString()
     * return value, which is essentially application request URI
     * minus the query string. The pattern is matched from the start
     * of the path string to the very end of it, so mind your prefix
     * and suffix slash. Since it is matched to a path string taken
     * from AppRequestURI, you don't have to enter your base path in
     * the URL.
     *
     * You can denote variables in your pattern by using this syntax:
     *
     * <code>
     * {label}
     * </code>
     * 
     * If you have a variable in your pattern, and you would like to
     * send it as an argument when calling your routine method, type
     * the placeholder name according to the current placeholder
     * prefix to do this (default placeholder prefix is '@'). For
     * example:
     *
     * <code>
     * $route = new BasicRoute('RouteID', array(
     *     'pattern' => '/blog/{id}/',
     *     'object' => 'App\Blog\PostController{Main:Transient}',
     *     'method' => 'showPost',
     *     'args' => array('@id')
     * ), $request, $appRequestURI);
     * </code>
     *
     * You can change the placeholder prefix by changing the 'prefix'
     * configuration. Other configurations are pretty straightforward,
     * 'object' is the routine object's instance name, 'method' is the
     * routine method to run, 'args' is an array containing arguments
     * to be passed to the routine method, while 'type' is the type of
     * the request.
     * 
     * Your variables can also denote rules. The available rules are:
     * 
     * <code>
     * {d:label} -> numerics.
     * {a:label} -> alphabets.
     * {b:label} -> alphanumerics.
     * {u:label} -> alphanumerics, underscores, and the minus character.
     * {(regex):label} -> regex rule inside the parentheses.
     * </code>
     * 
     * // TODO: Complete documentation here!
     * 
     * @param string $id
     * @param array $config
     * 
     */
    public function __construct($id, array $config, Request $request, AppRequestURI $appRequestURI)
    {
        $this->id = $id;
        $this->request = $request;
        $this->appRequestURI = $appRequestURI;
        $this->fillClassProperties($config);
        $this->variableMarkingsMustBeValid();
        $this->variableNamePattern = "/{$this->variablePrefix}([^{$this->variableSuffix}]+){$this->variableSuffix}/";
    }
    
    /**
     * Routes the request.
     *
     */
    public function route()
    {
        if ($this->requestTypeMismatch())
        {
            return;
        }
        
        $requestString = $this->appRequestURI->getPathString();
        $matchingPattern = $this->generateMatchingPattern();
        
        // Preliminary path string structure matching
        if (preg_match($matchingPattern, $requestString, $matches) <= 0)
        {
            return;
        }
        
        $variableValues = array_slice($matches, 1);
        $variableNames = $this->getVariableNamesFromPattern();
        
        // If no variables, then return callback since
        // the path string already passes the preliminary
        // string structure matching
        if (empty($variableValues) OR empty($variableNames))
        {
            return $this->returnCallback();
        }
        
        foreach ($variableNames as $index => $name)
        {
            if (!$this->passesVariableRequirements($name, $variableValues[$index]))
            {
                return;
            }
            
            $label = $this->getLabelFromVariable($name);
            $this->updateMethodArguments($label, $variableValues[$index]);
        }
        
        return $this->returnCallback();
    }
    
    public function getRelativePath(array $args)
    {
        $variableNames = $this->getVariableNamesFromPattern();
        $replacements = array();
        $patterns = array();
        
        foreach ($variableNames as $name)
        {   
            $label = $this->getLabelFromVariable($name);
            
            if (!array_key_exists($label, $args))
            {
                throw new InvalidArgumentException("BasicRoute error when getting relative path for route '{$this->id}', required argument '{$label}' doesn't exist.");
            }
            
            $patterns[] = $this->variableNamePattern;
            $replacements[] = $args[$label];
        }
        
        return preg_replace($patterns, $replacements, $this->pattern, 1);
    }
    
    /**
     * Sets the route ID of this route object.
     * 
     * @param string $id The router registration ID of this route object.
     * 
     */
    public function setID($id)
    {
        $this->id = $id;
    }
    
    /**
     * Fill default values for the constructor.
     * 
     * 
     * @see __construct()
     * 
     */
    protected function fillClassProperties($config)
    {
        $this->configMustBeValid($config);
        $this->pattern = $config['pattern'];
        $this->routineObjectInstanceName = $config['object'];
        $this->routineMethodName = $config['method'];
        
        if (!array_key_exists('type', $config))
        {
            $config['type'] = 'GET';
        }
        
        if (!array_key_exists('prefix', $config))
        {
            $config['prefix'] = '@';
        }
        
        if (!array_key_exists('args', $config))
        {
            $config['args'] = array();
        }
        
        if (!array_key_exists('varPrefix', $config))
        {
            $config['varPrefix'] = '{';
        }
        
        if (!array_key_exists('varSuffix', $config))
        {
            $config['varSuffix'] = '}';
        }
        
        $this->requestType = $config['type'];
        $this->placeholderPrefix = $config['prefix'];
        $this->routineMethodArgs = $config['args'];
        $this->variablePrefix = $config['varPrefix'];
        $this->variableSuffix = $config['varSuffix'];
    }
    
    /**
     * Makes sure the configuration array is valid.
     * 
     * Throws InvalidArgumentException if the configuration array
     * doesn't contain required indexes.
     * 
     * @see __construct()
     * @throws InvalidArgumentException
     * 
     */
    protected function configMustBeValid(array $config)
    {
        if (!array_key_exists('pattern', $config) OR
            !array_key_exists('object', $config) OR
            !array_key_exists('method', $config)
        )
        {
            throw new InvalidArgumentException("BasicRoute error in instantiation. The configuration array for route ID '{$this->id}' is invalid. Must contain the index 'pattern', 'object', 'method'.");
        }
        
        if (array_key_exists('type', $config) AND
            $config['type'] != 'GET' AND
            $config['type'] != 'POST'
        )
        {
            throw new InvalidArgumentException("BasicRoute error in instantiation. The 'type' configuration for route ID '{$this->id}' must be either 'GET' or 'POST'");
        }
        
        if (array_key_exists('args', $config) AND !is_array($config['args']))
        {
            throw new InvalidArgumentException("BasicRoute error in instantiation. The 'args' configuration for route ID '{$this->id}' must be an array.");
        }
    }
    
    protected function variableMarkingsMustBeValid()
    {
        if (empty($this->variablePrefix) OR empty($this->variableSuffix))
        {
            throw new InvalidArgumentException("BasicRoute error in instantiation. The variable prefix and suffix must not be empty.");
        }
        
        if (isset($this->variablePrefix{1}) OR isset($this->variablePrefix{1}))
        {
            throw new InvalidArgumentException("BasicRoute error in instantiation. The variable prefix and suffix's length must not be more than one character.");
        }
        
        $reservedCharacters = '/(\(|\)|\:|\/|\\\\)/';
        
        if (preg_match($reservedCharacters, $this->variablePrefix) > 0 OR preg_match($reservedCharacters, $this->variableSuffix) > 0)
        {
            throw new InvalidArgumentException("BasicRoute error in instantiation. The variable prefix and suffix must not be one of the reserved character '()|:\/'.");
        }
    }
    
    protected function requestTypeMismatch()
    {
        return (
            ($this->requestType == 'GET' AND !$this->request->isGetRequest()) OR
            ($this->requestType == 'POST' AND !$this->request->isPostRequest())
        );
    }
    
    /**
     * Generate a matching pattern.
     *
     * Matching pattern is the pattern used to match the application
     * request URI string with the basic structure of this route's
     * pattern. It does not check digits or alphanumeric flags yet, it
     * just makes sure that the overall pattern matches.
     *
     * To create a matching pattern, we replace this pattern:
     *
     * <code>
     * /{([^}]+)}/
     * </code>
     * 
     * with this wildcard pattern (anything but slash):
     * 
     * <code>
     * ([^\/]+)
     * </code>
     *
     * This method (like any other method in this class) uses slash
     * character as the regular expression delimiter, so every slash
     * in user's basic pattern is escaped beforehand.
     *
     * @return string The matching pattern.
     *
     */
    protected function generateMatchingPattern()
    {
        $matchingPattern = str_replace('/', '\/', $this->pattern);
        $matchingPattern = preg_replace($this->variableNamePattern, '([^\/]+)', $matchingPattern);
        $matchingPattern = "/^{$matchingPattern}$/D";
        return $matchingPattern;
    }
    
    /**
     * Get the variable names from the pattern.
     * 
     * Variables in the pattern is recognized by this pattern:
     * 
     * <code>
     * /{([^}]+)}/
     * </code>
     * 
     * The matches are returned without the variable prefix characters
     * (defaults to '{' and '}'). Example return array:
     *
     * <code>
     * $variableNames = array(
     *     'name',
     *     'd:digit',
     *     '(de|fr):lang'
     * );
     * </code>
     *
     * @return array The variable names in array.
     * 
     */
    protected function getVariableNamesFromPattern()
    {
        preg_match_all($this->variableNamePattern, $this->pattern, $matches);
        return $matches[1];
    }
    
    /**
     * Passes variable requirements.
     * 
     * 
     * @return bool True if passes, false if not.
     *
     */
    protected function passesVariableRequirements($name, $value)
    {
        if (substr($name, 0, 2) == 'd:')
        {
            return ctype_digit($value);
        }
        
        if (substr($name, 0, 2) == 'a:')
        {
            return ctype_alpha($value);
        }
        
        if (substr($name, 0, 2) == 'b:')
        {
            return ctype_alnum($value);
        }
        
        if (substr($name, 0, 2) == 'u:')
        {
            $value = str_replace('-', '', $value);
            $value = str_replace('_', '', $value);
            return ctype_alnum($value);
        }
        
        if (preg_match('/^(\([^\)]+\)):/', $name, $matches) > 0)
        {   
            $pattern = "/^{$matches[1]}$/D";
            
            if (preg_match($pattern, $value, $matches) <= 0)
            {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get label from variable name.
     * 
     * The variable name is a collation of rule requirement (optional)
     * and the variable label. The variable rule, if it exists, is
     * separated from the label by the colon (:) character. Example
     * variable name structures:
     *
     * <code>
     * label
     * d:label
     * (de|fr):label
     * </code>
     *
     * This method removes the rule prefix and return only the label
     * part of the variable name.
     * 
     * @param string $name The variable name.
     *
     */
    public function getLabelFromVariable($name)
    {   
        $colonCharLocation = strrpos($name, ':');
        
        if ($colonCharLocation === false)
        {
            return $name;
        }
        
        return substr($name, $colonCharLocation + 1);
    }
    
    /**
     * Updates method arguments with new variable value.
     * 
     * Replace placeholder values in method arguments with strings
     * taken from the current request path string.
     * 
     * @param string $label The placeholder label we are going to replace.
     * @param string $replacementValue The value we are going to replace with.
     *
     */
    protected function updateMethodArguments($label, $replacementValue)
    {
        $placeholderText = $this->placeholderPrefix . $label;
        
        foreach ($this->routineMethodArgs as $index => $value)
        {
            if ($value == $placeholderText)
            {
                $this->routineMethodArgs[$index] = $replacementValue;
            }
        }
    }
    
    /**
     * Constructs the callback and returns it.
     * 
     * Called by the route() method when it is sure that this request
     * matches the route configuration.
     * 
     * @see route()
     * @return Callback The callback instance.
     *
     */
    protected function returnCallback()
    {
        return new Callback(
            new ObjectReference($this->routineObjectInstanceName),
            $this->routineMethodName,
            $this->routineMethodArgs
        );
    }
}