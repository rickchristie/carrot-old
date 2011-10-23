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
 * Docs HTTP route.
 * 
 * Routes requests to {@see View::get()}.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Docs;

use Carrot\Core\Routing\Destination,
    Carrot\Core\Routing\HTTPURIInterface,
    Carrot\Core\Routing\Route\HTTPRouteInterface,
    Carrot\Core\DependencyInjection\Reference;

class HTTPRoute implements HTTPRouteInterface
{
    /**
     * @var string The path that this route should handle.
     */
    protected $path;
    
    /**
     * Constructor.
     * 
     * @param string $path The path that this route should handle.
     * 
     */
    public function __construct($path = '/docs/')
    {
        $path = trim($path, '/');
        $this->path = '/' . $path . '/';
    }
    
    /**
     * Routes the HTTP request into a Destination instance.
     * 
     * Greedily routes the request to {@see View::get()} if the
     * start of the request URI path matches the path given at
     * construction.
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
        $basePath = $baseHTTPURI->getPath();
        $requestPath = $requestHTTPURI->getPathWithoutBase($basePath);
        $guidePathEscaped = $this->escapePCREMetaCharacters($this->path);
        $pattern = "/^{$guidePathEscaped}((assets\\/)?(.+)){0,1}$/uD";
        
        if (preg_match_all($pattern, $requestPath, $matches) == FALSE)
        {
            return;
        }
        
        $isStaticAssetRequest = empty($matches[2][0]) == FALSE;
        $segments = explode('/', $matches[3][0]);
        $lastSegment = end($segments);
        $segmentCount = count($segments);
        
        if ($isStaticAssetRequest)
        {   
            if ($this->isValidStaticAssetRequest($lastSegment, $segmentCount) == FALSE)
            {
                return $this->get404Destination();
            }
            
            return new Destination(
                new Reference('Carrot\Docs\View'),
                'getStaticAsset',
                array($lastSegment)
            );
        }
        
        if ($this->isValidDocumentationRequest($lastSegment, $segments) == FALSE)
        {
            return $this->get404Destination();
        }
        
        unset($segments[$segmentCount-1]);
        return new Destination(
            new Reference('Carrot\Docs\View'),
            'getDocumentation',
            array($segments)
        );        
    }
    
    /**
     * Translates the given arguments into either an absolute or a
     * relative HTTP URI string.
     * 
     * The argument $args is essentially the URI path segments to be
     * appended to the base path.
     * 
     * @param array $args Arguments to generate the URI.
     * @param HTTPURIInterface $baseHTTPURI You can use this copy of
     *        the base HTTP URI to build the HTTPURIInterface object
     *        to be returned.
     * @param bool $absolute If TRUE, this method should return an
     *        absolute URI string, otherwise this method returns a
     *        relative URI string.
     * @return string
     *
     */
    public function getURI(array $args, HTTPURIInterface $baseHTTPURI, $absolute = FALSE)
    {   
        $isPathToStaticAsset = (isset($args[0]) AND $args[0] == 'assets');
        $pathToBeAppended = implode('/', $args);
        $pathToBeAppended = ltrim($pathToBeAppended, '/');
        $pathToBeAppended = $this->path . $pathToBeAppended;
        
        if ($isPathToStaticAsset == FALSE)
        {
            $pathToBeAppended = rtrim($pathToBeAppended, '/');
            $pathToBeAppended .= '/';
        }
        
        $baseHTTPURI->appendPath($pathToBeAppended);
        
        if ($absolute)
        {
            return $baseHTTPURI->getPathEncoded();
        }
        
        return $baseHTTPURI->get();
    }
    
    /**
     * Return a Destination instance that points to
     * {@see View::get404Page()}.
     *
     * @return Destination
     *
     */
    protected function get404Destination()
    {
        return new Destination(
            new Reference('Carrot\Docs\View'),
            'get404Page'
        );
    }
    
    /**
     * Check if the static asset request is valid or not.
     * 
     * Static asset requests are only valid if it does not contain
     * a trailing slash and the segment count is exactly 1 (the file
     * name only).
     *
     * The non-existence of trailing slash is necessary to force
     * one canonical URI for the resource and to make it look similar
     * to a regular file request.
     * 
     * @param string $lastSegment Last segment of the exploded
     *        request string. If empty, it means that the request
     *        string has a trailing slash.
     * @param int $segmentCount The number of segments from the
     *        formatted request string.
     * @return bool TRUE if valid, FALSE otherwise.
     *
     */
    protected function isValidStaticAssetRequest($lastSegment, $segmentCount)
    {   
        return (
            $lastSegment != '' AND
            $segmentCount == 1
        );
    }
    
    /**
     * Check if the documentation request is valid or not.
     * 
     * For regular document page requests, URIs must ALWAYS have a
     * trailing slash. This is so that we have only one canonical
     * URI.
     * 
     * Since the regex is greedy, so '/assets/' would be regarded as
     * a regular documentation page URI. We have to guard that.
     * 
     * @param string $lastSegment Last segment of the exploded
     *        request string. If empty, it means that the request
     *        string has a trailing slash.
     * @param array $segments The formatted request segments.
     * @return bool TRUE if valid, FALSE otherwise.
     *
     */
    protected function isValidDocumentationRequest($lastSegment, array $segments)
    {
        return (
            $lastSegment == '' AND
            $segments[0] != 'assets'
        );
    }
    
    /**
     * Escape all PCRE metacharacters on the given string.
     * 
     * This method Replaces all PCRE meta characters as listed in
     * {@see http://www.php.net/manual/en/regexp.reference.meta.php}
     * to its escaped counterpart. It also replaces the slash '/'
     * character since it is used as delimiters.
     * 
     * The reason this method exist is that we have to sidestep the
     * usage of non-multibyte-safe string functions like substr()
     * by using preg_* methods with '/u' modifier, which treats the
     * string as UTF-8. By sidestepping substr() method, multibyte
     * URIs that are encoded with UTF-8 should still work in this
     * class even without multibyte extension turned on.
     * 
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
        
        if ($replaced === NULL)
        {
            return $subject;
        }
        
        return $replaced;
    }
}