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
 * Uniform Resource Identifier.
 *
//---------------------------------------------------------------
 * Represents an URI that conforms to the "generic URI" syntax
 * based on RFC 2396 {@see http://www.ietf.org/rfc/rfc2396.txt}.
 * 
 * 
 * 
 * Represents the requets URI. Its will, as a default behavior,
 * try to guess the base URL and set it accordingly. Consequently
 * route classes that makes utilizes this class properly  will be
 * more host agnostic, as you can move your application to
 * different directories without breaking the routes.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Routing;

class URI
{
    /**
     * @var string Contents of $_SERVER['REQUEST_URI'].
     */
    protected $requestURI;
    
    /**
     * @var string Contents of $_SERVER['SCRIPT_NAME'].
     */
    protected $scriptName;
    
    /**
     * @var string Contents of $_SERVER['SERVER_NAME'].
     */
    protected $serverName;
    
    /**
     * @var string Relative path from the root of the server to the
     *      folder where Carrot's index.php file resides, with a
     *      trailing slash.
     */
    protected $basePath;
    
    /**
     * Constructor.
     * 
     * The base path is searched and removed from the request URI.
     * This is useful if your application is located inside a
     * subdirectory. For example, if the index.php file is located in
     * '/subdirectory/index.php' (relative from the server's root),
     * setting the base path into '/subdirectory/' will make this
     * class remove that part from the request URI before processing.
     *
     * If $basePath argument is set to NULL (as per default), this
     * class will attempt to guess the base path, assuming that the
     * main file is still named 'index.php'.
     * 
     * @param string $requestURI Contents of $_SERVER['REQUEST_URI'].
     * @param string $scriptName Contents of $_SERVER['SCRIPT_NAME'].
     * @param string $serverName Contents of $_SERVER['SERVER_NAME'].
     * @param string $basePath Relative path from the root of the
     *        server to the folder where Carrot's index.php file
     *        resides, with a trailing slash.
     *
     */
    public function __construct($server, $relativeURIBase = NULL, $baseAbsoluteURI = NULL)
    {
        if ($basePath == NULL)
        {
            $basePath = $this->guessBasePath();
        }
        
        $this->scriptName = $scriptName;
        $this->serverName = $serverName;
        $this->basePath = $basePath;
        $this->generateURIString();
        $this->generatePathString();
        $this->generateSegments();
    }
    
    /**
     * Guesses the base path.
     *
     * Assuming that each request will be handled by the 'index.php'
     * file, we use SCRIPT_NAME to get the path from the root to the
     * request handler. SCRIPT_NAME is used because it is available
     * to both Apache and IIS and are consistent in form (using
     * slashes as separator). Example transformation:
     * 
     * <code>
     * /index.php -> /
     * /carrot-dev/index.php -> /carrot-dev/
     * </code>
     * 
     * Base path returned will always have a trailing slash.
     *
     * @return string Base path (with trailing slash).
     *
     */
    protected function guessBasePath()
    {
        $path = str_ireplace('/index.php', '', $this->scriptName);
        
        if (empty($path) or substr($path, -1) != '/')
        {
            $path .= '/';
        }
        
        return $path;
    }
    
    /**
    //---------------------------------------------------------------
     * Generates request URI string.
     *
     * @see __construct()
     * @see getString()
     *
     */
    protected function generateString()
    {
        
    }
}