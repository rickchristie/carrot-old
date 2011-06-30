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
     * @var Request Instance of Request.
     */
    protected $request;
    
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
     * Construct the application request URI object.
     * 
     * If base path is not provided, this class will try to guess on
     * its own. Please note that it is safer to provide the base path
     * manually rather to let this class guess.
     *
     * @param Request $request Instance of Request.
     * @param string $basePath Relative path from the root to the folder where Carrot's index.php file resides, with trailing directory separator.
     *
     */
    public function __construct(Request $request, $basePath = null)
    {        
        if (empty($basePath))
        {
            $basePath = $this->guessBasePath();
        }
        
        $this->request = $request;
        $this->basePath = $basePath;
        $this->segments = $this->generateSegments();
        $this->segmentCount = count($this->segments);
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
     * it to array of uri segments.
     *
     * @return array Application URI segment array.
     *
     */
    public function generateSegments()
    {
        $appURIString = $this->server->getServer('REQUEST_URI');
        
        // Remove base path if it exists
        if (strpos($appURIString, $this->basePath) === 0)
        {
            $appURIString = substr($appURIString, strlen($this->basePath));
        }
        
        // Remove query string, if it exists
        $pos = strpos($appURIString, '?');
        
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
     * Guesses the base path.
     *
     * Assuming that each request will be handled by the 'index.php'
     * file, we use SCRIPT_NAME to get the path from the root to the
     * request handler. SCRIPT_NAME is used because it is available
     * to both Apache and IIS. 
     *
     * Base path returned will always have a trailing directory
     * separator.
     *
     * @return string Base path (with trailing slash).
     *
     */
    public function guessBasePath()
    {   
        $path = str_ireplace('/index.php', '', $this->server->getServer('SCRIPT_FILENAME'));
        
        if (empty($path) or substr($path, -1) != '/')
        {
            $path .= '/';
        }
        
        return $path;
    }
}