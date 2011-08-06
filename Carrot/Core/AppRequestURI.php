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
 * Application Request URI
 * 
 * Application request URI is segments of request URIs with the
 * base path removed. Using this class creates consistent routing,
 * even if you move the application to another folder.
 *
 * Request URI generally includes the base path, for example, if
 * the framework is located at '/webapp/', the request URI will
 * include the base path, as in '/webapp/product/list/'. Unlike
 * the request URI, application request URI removes the base path
 * so you have a consistent request URI. For the example above,
 * it will consistently be '/product/list/' even after you changed
 * the base path. This makes routing easier.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class AppRequestURI
{
    /**
     * @var string The contents of $_SERVER['SCRIPT_NAME'], injected at construction.
     */
    protected $serverScriptName;
    
    /**
     * @var string The contents of $_SERVER['REQUEST_URI'], injected at construction.
     */
    protected $serverRequestURI;
    
    /**
     * @var string Relative path from the root to the folder where Carrot's index.php file resides, with trailing directory separator.
     */
    protected $basePath;
    
    /**
     * @var array Segments of application request URI.
     */
    protected $segments;
    
    /**
     * @var int Total number of segments this application request URI has.
     */
    protected $segmentCount;
    
    /**
     * @var string The application request URI in string.
     */
    protected $string;
    
    /**
     * Construct the application request URI object.
     * 
     * If base path is not provided, this class will try to guess on
     * its own. Please note that it is safer to provide the base path
     * manually rather to let this class guess.
     * 
     * <code>
     * $appRequestURI = new AppRequestURI($request, '/base/path/to/framework/');
     * </code>
     *
     * @param string $serverScriptName The contents of $_SERVER['SCRIPT_NAME'].
     * @param string $serverRequestURI The contents of $_SERVER['REQUEST_URI'].
     * @param string $basePath Relative path from the root to the folder where Carrot's index.php file resides, with trailing directory separator.
     *
     */
    public function __construct($serverScriptName, $serverRequestURI, $basePath = null)
    {
        $this->serverScriptName = $serverScriptName;
        $this->serverRequestURI = $serverRequestURI;
                
        if (empty($basePath))
        {
            $basePath = $this->guessBasePath();
        }
        
        $this->basePath = $basePath;
        $this->segments = $this->generateSegments();
        $this->segmentCount = count($this->segments);
        $this->string = $this->generateString();
    }
    
    /**
     * Returns true if the array give equals the segments array.
     *
     * Comparison is done using the identity comparison operator (the
     * triple equal sign '==='). This means both must have the same
     * key/value pairs, in the same order, and in the same type.
     * 
     * @see http://www.php.net/manual/en/language.operators.array.php
     * @param array $array The array to be compared.
     * @return bool True or false.
     *
     */
    public function segmentMatches(array $array)
    {
        return ($this->segments === $array);
    }
    
    /**
     * Returns a segment from the application request URI.
     *
     * @param int $index The segment index.
     * @return string Segment with the corresponding index.
     *
     */
    public function getSegment($index)
    {
        if (isset($this->segments[$index]))
        {
            return $this->segments[$index];
        }
    }
    
    /**
     * Returns the whole application request URI segment array.
     *
     * @return array Application request URI segment array.
     *
     */
    public function getSegments()
    {
        return $this->segments;
    }
    
    /**
     * Returns the application request URI string.
     *
     * The application request URI string is similar to the
     * REQUEST_URI except that it doesn't contain the base path.
     * 
     * @return string Application request URI string.
     *
     */
    public function getString()
    {
        return $this->string;
    }
    
    /**
     * Returns the total number of segments the application request URI has.
     * 
     * @return int Count of the application URI segments.
     * 
     */
    public function getSegmentCount()
    {
        return $this->segmentCount;
    }
    
    /**
     * Returns the base path in string.
     *
     * @return string The base path.
     *
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
    
    /**
     * Guesses the base path.
     *
     * Assuming that each request will be handled by the 'index.php'
     * file, we use SCRIPT_NAME to get the path from the root to the
     * request handler. SCRIPT_NAME is used because it is available
     * to both Apache and IIS and are consistent in form (using
     * slashes as separator):
     * 
     * <code>
     * /index.php -> /
     * /carrot-dev/index.php -> /carrot-dev/
     * </code>
     * 
     * Base path returned will always have a trailing directory
     * separator.
     *
     * @return string Base path (with trailing slash).
     *
     */
    protected function guessBasePath()
    {
        $path = str_ireplace('/index.php', '', $this->serverScriptName);
        
        if (empty($path) or substr($path, -1) != '/')
        {
            $path .= '/';
        }
        
        return $path;
    }
    
    /**
     * Generates the application URI segments.
     *
     * Application request URI is different from Request URI in that
     * it doesn't include the base path. If your base path is
     * '/base/path/' and your the request uri is '/base/path/item/id',
     * the application request URI array will be:
     *
     * <code>
     * array('item', 'id')
     * </code> 
     *
     * This method checks if base path is in the REQUEST_URI. If it
     * doesn't, it throws a RuntimeException. It then proceeds to
     * cut query string and base path from REQUEST_URI, then exploding
     * it to array of uri segments. Query strings will not be part of
     * the segment.
     *
     * @return array Application request URI segment array.
     *
     */
    protected function generateSegments()
    {
        $appURIString = $this->serverRequestURI;
        
        // Remove base path if it exists
        if (strrpos($appURIString, $this->basePath) === 0)
        {
            $appURIString = substr($appURIString, strlen($this->basePath));
        }
        
        // Remove query string, if it exists
        $pos = strrpos($appURIString, '?');
        
        if ($pos !== false)
        {
            $appURIString = substr($appURIString, 0, $pos);
        }
        
        // Explode it to segments
        $appURIStringExploded = explode('/', $appURIString);
        $appRequestURISegments = array();
        
        foreach ($appURIStringExploded as $segment)
        {
            if (!empty($segment))
            {
                $appRequestURISegments[] = urldecode($segment);
            }
        }
        
        return $appRequestURISegments;
    }
    
    /**
     * Constructs a string similar to REQUEST_URI, but without base path.
     * 
     * This method removes the base path from the REQUEST_URI to
     * create an application request URI. Unlike segments, query
     * string will be retained as part of the string. The string this
     * method produces will be similar to REQUEST_URI only without the
     * base path.
     * 
     * @return string Application request URI in one string.
     * 
     */
    protected function generateString()
    {
        $appRequestURIString = $this->serverRequestURI;
        
        // Removes base path if it exists
        if (strrpos($appRequestURIString, $this->basePath) === 0)
        {
            $appRequestURIString = substr($appRequestURIString, strlen($this->basePath));
        }
        
        if (empty($appRequestURIString) or substr($appRequestURIString, 0, 1) != '/')
        {
            $appRequestURIString = '/' . $appRequestURIString;
        }
        
        return $appRequestURIString;
    }
}