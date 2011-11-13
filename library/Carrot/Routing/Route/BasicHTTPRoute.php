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
 * Basic HTTP route.
 *
 * This class provides ready to use, basic HTTP routing
 * capabilities by matching the 'path' segment of the URI. It
 * uses the base URI given by the Router to do host/location
 * agnostic two-way routing. In order for this class to work
 * properly, the base URI configuration must be set correctly or
 * guessed correctly by the Router.
 *
 * This class uses the unicode '/u' modifier in all its PCRE
 * method usage. It means this class supports UTF-8 encoding
 * only.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Routing\Route;

use InvalidArgumentException,
    Carrot\Request\RequestInterface,
    Carrot\Routing\Destination,
    Carrot\Routing\HTTPURIInterface;

class BasicHTTPRoute implements HTTPRouteInterface
{
    /**
     * @var array The configuration array.
     */
    protected $config;
    
    /**
     * @var RequestInterface Represents the current request.
     */
    protected $request;
    
    /**
     * @var string The regular expression to check the structure of
     *      the URI's path and extract placeholder names and values.
     */
    protected $pathStructureRegex = '';
    
    /**
     * @var array Contains placeholder '<label>' as keys and its
     *      replacement values as content,
     *      {@see generateMethodArgs()}.
     */
    protected $argsPlaceholderReplacements = array();
    
    /**
     * Constructor.
     * 
     * The configuration array must contain at least the pattern, the
     * DIC reference, and the method:
     *
     * <code>
     * $route = new BasicHTTPRoute('RouteID',
     *     array(
     *         'pattern' => '/',
     *         'reference' => new Reference('Sample\Welcome'),
     *         'method' => 'getWelcomeResponse'
     *     ),
     *     $request
     * );
     * </code>
     * 
     * Here is an example of a full configuration value construction:
     * 
     * <code>
     * $route = new BasicHTTPRoute('RouteID',
     *     array(
     *         'pattern' => '/blog/<id>/<slug>/',
     *         'reference' => new Reference('App\Blog\PostController'),
     *         'method' => 'showPost',
     *         'args' => array('<id>', '<slug>'),
     *         'type' => 'GET'
     *     ),
     *     $request
     * );
     * </code>
     *
     * You can denote placeholders in your pattern by using this
     * syntax:
     *
     * <code>
     * <label>
     * </code>
     * 
     * Use only alphanumeric characters in your labels, otherwise you
     * risk a conflict in the parsing/matching process, leading to
     * weird behavior.
     * 
     * You can then send the contents of the placeholder as an
     * argument. Type the placeholder label with the placeholder
     * prefix and sufix as the argument and it will be replaced by
     * the contents of the placeholder:
     * 
     * <code>
     * $route = new BasicHTTPRoute('RouteID',
     *     array(
     *         'pattern' => '/blog/<id>/<u:slug>/',
     *         'reference' => $postControllerReference,
     *         'method' => 'showPost',
     *         'args' => array('<id>', '<u:slug>')
     *     ),
     *     $request
     * );
     * </code>
     * 
     * You can tell the route object to only route 'GET', 'POST', or
     * 'XMLHTTPRequest' request by modifying the 'type' item in the
     * configuration array.
     * 
     * You placeholders can contain rules. Available rules are:
     * 
     * <code>
     * <label> -> No rules, accepts anything other than slash '/'.
     * <d:label> -> Numerics.
     * <a:label> -> Alphabets.
     * <b:label> -> Both alphabets and numerics.
     * <u:label> -> Alphanumerics, underscores '_', and minus '-'.
     * <r:label:regex> -> Regular expression.
     * </code>
     *
     * The regex rule can be used for simple expressions, such as
     * this:
     *
     * <code>
     * $pattern = '/blog/<id>/lang/<(de|en|fr)>:<lang>/';
     * </code>
     * 
     * The above example will be converted to this regex:
     *
     * <code>
     * /^(de|en|fr)$/uD
     * </code>
     * 
     * Note that due to implementation detail, you will not be able
     * to use slash '/' character in your regex. Also, since the
     * colon ':' character is used to separate the placeholder label
     * from the regex, if you need to use it you will have to escape
     * it, as in '\:'.
     *
     * If your regex contains parentheses, you can reference it using
     * in your arguments array using the syntax below:
     *
     * <code>
     * '<r:label>' -> Replaced by the whole path segment.
     * '<r:label@1>' -> Replaced by the contents of parentheses 1.
     * </code>
     * 
     * @param array $config Configuration array.
     * @param RequestInterface $request Represents the current
     *        request.
     *
     */
    public function __construct(array $config, RequestInterface $request)
    {
        $this->request = $request;
        $this->config = $config;
        $this->writeDefaultsToConfig();
        $this->validateConfig();
        $this->generatePathStructureRegex();
    }
    
    /**
     * Routes the HTTP request into a Destination instance.
     * 
     * The pattern is matched using
     * {@see HTTPURIInterface::pathMatches()}, while passing the base
     * path from the given base HTTP URI as an argument. This allows
     * the routing to be done in a host/location agnostic way.
     *
     * The process of routing the request is as follows:
     *
     * <ul>
     *     <li>
     *         Checks if the type of the request matches.
     *     </li>
     *     <li>
     *         Checks the structure of the path, see if it matches
     *         the structure provided by the pattern.
     *     </li>
     *     <li>
     *         Checks individual placeholder rules in the pattern,
     *         while also creating argument replacement array
     *         {@see $argsPlaceholderReplacements}.
     *     </li>
     *     <li>
     *         If everything matches, construct the Destination
     *         instance to be returned.
     *     </li>
     * </ul>
     *
     * @see Destination
     * @param HTTPURIInterface $requestHTTPURI
     * @param HTTPURIInterface $baseHTTPURI
     * @return Destination|NULL An instance of Destination if the
     *         route matches the request, NULL otherwise.
     *
     */
    public function route(HTTPURIInterface $requestHTTPURI, HTTPURIInterface $baseHTTPURI)
    {   
        if ($this->doesRequestTypeMatch() == FALSE)
        {
            return;
        }
        
        $basePath = $baseHTTPURI->getPath();
        $path = $requestHTTPURI->getPathWithoutBase($basePath);
        
        if ($this->doesRequestPathMatchesStructureRegex($path) == FALSE)
        {
            return;
        }
        
        if ($this->doesIndividualPlaceholderRulesMatch($path) == FALSE)
        {
            return;
        }
        
        $args = $this->generateMethodArgs();
        return new Destination(
            $this->config['reference'],
            $this->config['method'],
            $args
        );
    }
    
    /**
     * Translates the given arguments into either an absolute or a
     * relative HTTP URI string.
     * 
     * When calling this method (via the Router or otherwise), pass
     * the arguments array with placeholder labels as index and the
     * values as the content. For example, in this route pattern:
     *
     * <code>
     * /post/<d:id>/<r:lang><(en|de|fr)>/
     * </code>
     *
     * You will have to provide two arguments, 'id', and 'lang', as
     * in:
     *
     * <code>
     * $args = array(
     *     '458934',
     *     'de'
     * );
     * </code>
     * 
     * This method will not validate your arguments, so make sure to
     * pass the arguments in the correct order.
     * 
     * @param array $args Arguments to generate the URI.
     * @param HTTPURIInterface $baseHTTPURI Used generate the URI.
     * @param bool $absolute If TRUE, this method should return an
     *        absolute URI string, otherwise this method returns a
     *        relative URI string.
     * @return string
     *
     */
    public function getURI(array $args, HTTPURIInterface $baseHTTPURI, $absolute = FALSE)
    {
        if (empty($args) == FALSE)
        {
            $patterns = array_fill(0, count($args), '/<[^\\/>]+>/u');
            $pathToBeAppended = preg_replace($patterns, $args, $this->config['pattern'], 1);
        }
        else
        {
            $pathToBeAppended = $this->config['pattern'];
        }
        
        if ($pathToBeAppended != '')
        {
            $baseHTTPURI->appendPath($pathToBeAppended);
        }
        
        if ($absolute)
        {
            return $baseHTTPURI->get();
        }
        else
        {
            return $baseHTTPURI->getPathEncoded();
        }
    }
    
    /**
     * Writes default values to configuration items that aren't
     * required, if they are not specified.
     * 
     * @see __construct()
     *
     */
    protected function writeDefaultsToConfig()
    {
        if (array_key_exists('type', $this->config) == FALSE)
        {
            $this->config['type'] = '*';
        }
        
        if (array_key_exists('args', $this->config) == FALSE)
        {
            $this->config['args'] = array();
        }
    }
    
    /**
     * Validate this class's configuration array.
     *
     * @see __construct()
     * @throws InvalidArgumentException If the configuration array
     *         failed to pass the validation process.
     *
     */
    protected function validateConfig()
    {
        if ($this->doRequiredKeysExistInConfig() == FALSE)
        {
            throw new InvalidArgumentException('BasicHTTPRoute error in instantiation. The configuration array must contain the index \'pattern\', \'reference\', \'method\'.');
        }
        
        if ($this->isTypeValidInConfig() == FALSE)
        {
            throw new InvalidArgumentException("BasicHTTPRoute error in instantiation. The configuration item 'type' must be either '*', 'GET', or 'POST'. '{$this->config['type']} given.'");
        }
        
        if (!is_array($this->config['args']))
        {
            $type = gettype($this->config['args']);
            throw new InvalidArgumentException("BasicHTTPRoute error in instantiation. The configuration item 'args' must be an array, {$type} given.");
        }
    }
    
    /**
     * Checks if the required keys exists in this class's
     * configuration array.
     * 
     * @see validateConfig()
     * @return bool TRUE if all required keys exist, FALSE otherwise.
     *
     */
    protected function doRequiredKeysExistInConfig()
    {
        return (
            array_key_exists('pattern', $this->config) AND
            array_key_exists('reference', $this->config) AND
            array_key_exists('method', $this->config)
        );
    }
    
    /**
     * Checks if the 'type' configuration item is valid or not.
     *
     * @see validateConfig()
     * @return bool TRUE if valid, FALSE otherwise.
     *
     */
    protected function isTypeValidInConfig()
    {
        return (
            $this->config['type'] == 'GET' OR
            $this->config['type'] == 'POST' OR
            $this->config['type'] == 'XMLHTTPRequest' OR
            $this->config['type'] == '*'
        );
    }
    
    /**
     * Generates regular expression pattern for matching the
     * structure of the path and extracting placeholders and their
     * values.
     * 
     * It first replaces all placeholders with an alphanumeric hash
     * string, as in changing this:
     *
     * <code>
     * /path/[subpath]/<d:id>/<u:slug>/<r:lang><(en|de|fr)>/
     * </code>
     *
     * to this:
     *
     * <code>
     * /path/[subpath]/4e9c0702a58a2/4e9c0702a58a2/4e9c0702a58a2/
     * </code>
     *
     * The reason we use hash is so that the placeholders (which may
     * contain regex pattern of its own) are untouched in the
     * escaping process.
     * 
     * Then it escapes all PCRE metacharacters, including slash '/',
     * which is used as delimiter in all regex pattern in this class:
     * 
     * <code>
     * \/path\/\[subpath\]\/4e9c0702a58a2\/4e9c0702a58a2\/4e9c0702a58a2\/
     * </code>
     * 
     * and finally, replaces the hash with the wildcard matching
     * pattern, that matches anything but slash '([^\\/]+)':
     *
     * <code>
     * \/path\/\[subpath\]\/([^\\/]+)\/([^\\/]+)\/([^\\/]+)\/
     * </code>
     *
     * then the delimiters and modifiers are added, resulting in a
     * regex pattern that we can use to match the general path
     * structure and extract placeholders and their values:
     *
     * <code>
     * /^\/path\/\[subpath\]\/([^\\/]+)\/([^\\/]+)\/([^\\/]+)\/$/uD
     * </code>
     * 
     * @see route()
     *
     */
    protected function generatePathStructureRegex()
    {
        if (empty($this->pathStructureRegex) == FALSE)
        {
            return;
        }
        
        // Escaping the pattern string
        $hash = uniqid('hashStringForReplacements');
        $pathStructureRegex = $this->config['pattern'];
        $patternForMatchingPlaceholders = '/<[^\\/>]+>/u';
        $pathStructureRegex = preg_replace($patternForMatchingPlaceholders, $hash, $pathStructureRegex);
        $pathStructureRegex = $this->escapePCREMetaCharacters($pathStructureRegex);
        
        // Replacing hash with the wildcard pattern
        $wildcardRegex = '([^\\/]+)';
        $pathStructureRegex = preg_replace("/{$hash}/u", $wildcardRegex, $pathStructureRegex);
        $this->pathStructureRegex = '/^' . $pathStructureRegex . '$/uD';
    }
    
    /**
     * Escape all PCRE metacharacters on the given string.
     * 
     * This method Replaces all PCRE meta characters as listed in
     * {@see http://www.php.net/manual/en/regexp.reference.meta.php}
     * to its escaped counterpart. It also replaces the slash '/'
     * character since it is used as delimiters.
     * 
     * @see generatePathStructureRegex()
     * @param string $string The string to be escaped.
     * @return string The escaped string if it's successful, or the
     *         original string if it fails.
     * 
     */
    protected function escapePCREMetaCharacters($string)
    {
        $patterns = array(
            '/\\//u', // Search for '/'
            '/\\\\/u', // Search for '\'
            '/\\^/u', // Search for '^'
            '/\\$/u', // Search for '$'
            '/\\./u', // Search for '.'
            '/\\[/u', // Search for '['
            '/\\]/u', // Search for ']'
            '/\\|/u', // Search for '|'
            '/\\(/u', // Search for '('
            '/\\(/u', // Search for ')'
            '/\\*/u', // Search for '*'
            '/\\+/u', // Search for '+'
            '/\\{/u', // Search for '{'
            '/\\}/u', // Search for '}'
            '/\\-/u', // Search for '-'
        );
        
        $replacements = array(
            '\\/', // Replaces '/'
            '\\\\', // Replaces '\'
            '\\^', // Replaces '^'
            '\\$', // Replaces '$'
            '\\.', // Replaces '.'
            '\\[', // Replaces '['
            '\\]', // Replaces ']'
            '\\|', // Replaces '|'
            '\\(', // Replaces '('
            '\\)', // Replaces ')'
            '\\*', // Replaces '*'
            '\\+', // Replaces '+'
            '\\{', // Replaces '{'
            '\\}', // Replaces '}'
            '\\-', // Replaces '-'
        );
        
        return $this->PCREReplace($patterns, $replacements, $string);
    }
    
    /**
     * Run the preg_replace() function with the given arguments, but
     * wraps it in try catch block.
     * 
     * Either return the original subject (because of no match or
     * error) or return the replaced subject.
     *
     * @param string $pattern As in preg_replace().
     * @param string $replacement As in preg_replace().
     * @param string $subject As in preg_replace().
     * @param int $limit As in preg_replace().
     * @return string
     *
     */
    protected function PCREReplace($pattern, $replacement, $subject, $limit = -1)
    {
        try
        {
            $replaced = preg_replace($pattern, $replacement, $subject, $limit);
        }
        catch(Exception $exception)
        {
            // Failed to run the replace method, most likely
            // the path string is not in a safe format for 
            // preg_replace().
            return $subject;
        }
        
        if ($replaced != NULL)
        {
            return $replaced;
        }
        
        return $subject;
    }
    
    /**
     * Checks whether the request type matches this route's rule.
     * 
     * @see route()
     *
     */
    protected function doesRequestTypeMatch()
    {
        return (
            $this->config['type'] == '*' OR
            ($this->config['type'] == 'GET' AND $this->request->isGet()) OR
            ($this->config['type'] == 'POST' AND $this->request->isPost()) OR
            ($this->config['type'] == 'XMLHTTPRequest' AND $this->request->isXMLHTTPRequest())
        );
    }
    
    /**
     * Checks whether the structure of the request URI's path matches
     * the structure of the pattern.
     *
     * @see route()
     * @param string $path The request path to be matched with the
     *        pattern.
     *
     */
    protected function doesRequestPathMatchesStructureRegex($path)
    {
        return preg_match($this->pathStructureRegex, $path);
    }
    
    /**
     * Loops through each individual placeholder rule and checks
     * them against the placeholder values.
     * 
     * Other than checking the rule, it also saves each argument
     * replacements in {@see $argsPlaceholderReplacements}, ready
     * to be used by {@see generateMethodArgs()}.
     * 
     * @see route()
     * @param string $path the request path where the placeholder
     *        values can be extracted.
     *
     */
    protected function doesIndividualPlaceholderRulesMatch($path)
    {
        $placeholders = $this->extractPlaceholders();
        $values = $this->extractPlaceholderValues($path);
        $regexRulePattern = '/^<r:([^\\/<]+)(?<!\\\\):(.+)>$/uD';
        $normalRulePattern = '/^<(d|a|b|u):([^\\/>]+)>$/uD';
        $noRulePattern = '/^<([^\\/>]+)>$/uD';
        
        if ($this->arePlaceholdersAndValuesValid($placeholders, $values) == FALSE)
        {
            return FALSE;
        }
        
        foreach ($placeholders as $key => $placeholder)
        {
            $isRegexRule = preg_match_all($regexRulePattern, $placeholder, $matches);
            
            if ($isRegexRule >= 1)
            {
                $pattern = "/^{$matches[2][0]}\$/uD";
                $label = $matches[1][0];
                $subject = $values[$key];
                
                if ($this->runRegexRule($pattern, $subject, $label))
                {
                    continue;
                }
                
                return FALSE;
            }
            
            $isNormalRule = preg_match_all($normalRulePattern, $placeholder, $matches);
            
            if ($isNormalRule >= 1)
            {
                $typeChar = $matches[1][0];
                $label = $matches[2][0];
                $subject = $values[$key];
                
                if ($this->runNormalRule($typeChar, $subject, $label))
                {
                    continue;
                }
                
                return FALSE;
            }
            
            $isNoRule = preg_match_all($noRulePattern, $placeholder, $matches);
            
            if ($isNoRule >= 1)
            {
                $label = "<{$matches[1][0]}>";
                $subject = $values[$key];
                $this->argsPlaceholderReplacements[$label] = $subject;
                continue;
            }
            
            // We don't understand or can't
            // parse the placeholder syntax
            return FALSE;
        }
        
        return TRUE;
    }
    
    /**
     * Run the regex pattern found in the placeholder rule.
     * 
     * Also saves the entire string and the parentheses to
     * {@see $argsPlaceholderReplacements}.
     * 
     * @see doesIndividualPlaceholderRulesMatch()
     * @param string $pattern The pattern found in the placeholder.
     * @param string $subject The subject to be matched against the
     *        found pattern.
     * @param string $label The label of the placeholder (without
     *        '<' prefix and '>' suffix).
     *
     */
    protected function runRegexRule($pattern, $subject, $label)
    {
        $found = preg_match_all($pattern, $subject, $matches);
        
        if ($found < 1)
        {
            return FALSE;
        }
        
        foreach ($matches as $key => $match)
        {
            if ($key == 0)
            {
                continue;
            }
            
            $parenthesesLabel = "<r:{$label}@{$key}>";
            $this->argsPlaceholderReplacements[$parenthesesLabel] = $match[0];
        }
        
        $this->argsPlaceholderReplacements["<r:{$label}>"] = $subject;
        return TRUE;
    }
    
    /**
     * Run normal character type rules.
     *
     * @see doesIndividualPlaceholderRulesMatch()
     * @param string $typeChar One character code that determines the
     *        type of rule that is being applied.
     * @param string $subject The subject to be matched against the
     *        character type rule.
     * @param string $label The label of the placeholder (without
     *        '<' prefix and '>' suffix).
     *
     */
    protected function runNormalRule($typeChar, $subject, $label)
    {
        $isValid = TRUE;
        $label = "<{$typeChar}:{$label}>";
        
        switch ($typeChar)
        {
            case 'd':
                $isValid = ctype_digit($subject);
            break;
            case 'a':
                $isValid = ctype_alpha($subject);
            break;
            case 'b':
                $isValid = ctype_alnum($subject);
            break;
            case 'u':
                $patterns = array('/_/uD', '/\\-/uD');
                $subjectStripped = preg_replace($patterns, '', $subject);
                $isValid = ctype_alnum($subjectStripped);
            break;
        }
        
        $this->argsPlaceholderReplacements[$label] = $subject;
        return $isValid;
    }
    
    /**
     * Extracts placeholders from the pattern using the path
     * structure regex {@see $pathStructureRegex}.
     *
     * The placeholders are returned as an array and should mirror
     * the results of {@see extractPlaceholderValues()}:
     *
     * <code>
     * $placeholders = array(
     *     '<d:id>',
     *     '<u:title>'
     * );
     * </code>
     * 
     * @see doesIndividualPlaceholderRulesMatch()
     *
     */
    protected function extractPlaceholders()
    {
        $placeholders = array();
        $found = preg_match_all($this->pathStructureRegex, $this->config['pattern'], $matches);
        
        if ($found < 1)
        {
            return FALSE;
        }
        
        foreach ($matches as $key => $match)
        {
            // The first key contains the entire
            // string that matches.
            if ($key == 0)
            {
                continue;
            }
            
            $placeholders[] = $match[0];
        }
        
        return $placeholders;
    }
    
    /**
     * Extracts placeholder values from the pattern using the path
     * structure regex {@see $pathStructureRegex}.
     * 
     * The values are returned as an array and should mirror the
     * results of {@see extractPlaceholders()}:
     *
     * <code>
     * $values = array(
     *     '346652',
     *     'blog-title-or-slug'
     * );
     * </code>
     * 
     * @see doesIndividualPlaceholderRulesMatch()
     * @param string $path the request path where the placeholder
     *        values can be extracted.
     *
     */
    protected function extractPlaceholderValues($path)
    {
        $values = array();
        $found = preg_match_all($this->pathStructureRegex, $path, $matches);
        
        if ($found < 1)
        {
            return FALSE;
        }
        
        foreach ($matches as $key => $match)
        {
            // The first key contains the entire
            // string that matches.
            if ($key == 0)
            {
                continue;
            }
            
            $values[] = $match[0];
        }
        
        return $values;
    }
    
    /**
     * Check whether the placeholders and values derived from
     * {@see extractPlaceholders()} and
     * {@see extractPlaceholderValues()} match one another.
     *
     * @param string $placeholders
     * @param string $values
     * @return bool TRUE if matches, FALSE otherwise.
     *
     */
    protected function arePlaceholdersAndValuesValid($placeholders, $values)
    {
        return (
            is_array($placeholders) AND
            is_array($values) AND
            count($values) == count($placeholders)
        );
    }
    
    /**
     * Generates method arguments.
     *
     * This method operates on the assumption that the class property
     * {@see $argsPlaceholderReplacements} has been correctly filled
     * by {@see doesIndividualPlaceholderRulesMatch()} operations. If
     * correctly filled, the array should have this structure:
     *
     * <code>
     * $argsPlaceholderReplacements = array(
     *     '<id>' => '38593',
     *     '<slug>' => 'the-blog-post-slug',
     *     '<pattern>' => 'this-is-the-title.html',
     *     '<pattern@1>' => 'this-is-the-title',
     *     '<pattern@2>' => '.html'
     * );
     * </code>
     *
     * @see route()
     *
     */
    protected function generateMethodArgs()
    {
        $methodArgs = array();
        
        foreach ($this->config['args'] as $arg)
        {
            if (array_key_exists($arg, $this->argsPlaceholderReplacements))
            {
                $methodArgs[] = $this->argsPlaceholderReplacements[$arg];
                continue;
            }
            
            $methodArgs[] = $arg;
        }
        
        return $methodArgs;
    }
}