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
 * Carrot's default request implementation.
 *
 * Provides access to request environment variables (PHP
 * superglobals), along with some other methods that helps you
 * inspect the nature of the request. This object is immutable.
 * This object, although part of Carrot's core, is a standalone
 * object that should be general enough for use outside of
 * Carrot.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Request;

class DefaultRequest implements RequestInterface
{
    /**
     * @var array Wrapper for $_SERVER.
     */
    protected $server;
    
    /**
     * @var array Wrapper for $_GET.
     */
    protected $get;
    
    /**
     * @var array Wrapper for $_POST.
     */
    protected $post;
    
    /**
     * @var array Wrapper for $_FILES.
     */
    protected $files;
    
    /**
     * @var array Wrapper for $_COOKIE.
     */
    protected $cookie;
    
    /**
     * @var array Wrapper for $_REQUEST.
     */
    protected $request;
    
    /**
     * @var array Wrapper for $_ENV.
     */
    protected $env;
    
    /**
     * @var array Contains request header keys and values,
     *      {@see initializeHeaders()}.
     */
    protected $headers;
    
    /**
     * Constructs the Request object.
     *
     * @param array $server $_SERVER variable.
     * @param array $get $_GET variable.
     * @param array $post $_POST variable.
     * @param array $files $_FILES variable.
     * @param array $cookie $_COOKIE variable.
     * @param array $request $_REQUEST variable.
     * @param array $env $_ENV variable.
     *
     */
    public function __construct($server, $get, $post, $files, $cookie, $request, $env)
    {
        $this->server = $server;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookie = $cookie;
        $this->request = $request;
        $this->env = $env;
        $this->initializeHeaders();
    }
    
    /**
     * Checks if the current request is a GET request.
     * 
     * Uses $_SERVER['SERVER_METHOD'], depending on the server
     * environment you have, this method may or may not work.
     *
     * @return bool TRUE if a GET request, FALSE otherwise.
     *
     */
    public function isGet()
    {
        return ($this->getServer('REQUEST_METHOD') == 'GET');
    }
    
    /**
     * Checks if the current request is a POST request.
     * 
     * Uses $_SERVER['SERVER_METHOD'], depending on the server
     * environment you have, this method may or may not work.
     *
     * @return bool TRUE if a POST request, FALSE otherwise.
     *
     */
    public function isPost()
    {
        return ($this->getServer('REQUEST_METHOD') == 'POST');
    }
    
    /**
     * Checks if the current request is a PUT request.
     * 
     * Uses $_SERVER['SERVER_METHOD'], depending on the server
     * environment you have, this method may or may not work.
     *
     * @return bool TRUE if a PUT request, FALSE otherwise.
     *
     */
    public function isPut()
    {
        return ($this->getServer('REQUEST_METHOD') == 'PUT');
    }
    
    /**
     * Checks if the current request is a DELETE request.
     * 
     * Uses $_SERVER['SERVER_METHOD'], depending on the server
     * environment you have, this method may or may not work.
     *
     * @return bool TRUE if a DELETE request, FALSE otherwise.
     *
     */
    public function isDelete()
    {
        return ($this->getServer('REQUEST_METHOD') == 'DELETE');
    }
    
    /**
     * Checks if the current request is a HEAD request.
     * 
     * Uses $_SERVER['SERVER_METHOD'], depending on the server
     * environment you have, this method may or may not work.
     *
     * @return bool TRUE if a HEAD request, FALSE otherwise.
     *
     */
    public function isHead()
    {
        return ($this->getServer('REQUEST_METHOD') == 'HEAD');
    }
    
    /**
     * Checks if the current request is a OPTIONS request.
     * 
     * Uses $_SERVER['SERVER_METHOD'], depending on the server
     * environment you have, this method may or may not work.
     *
     * @return bool TRUE if an OPTIONS request, FALSE otherwise.
     *
     */
    public function isOptions()
    {
        return ($this->getServer('REQUEST_METHOD') == 'OPTIONS');
    }
    
    /**
     * Check if the header X_REQUESTED_WITH exists and its value is
     * 'XMLHttpRequest'.
     *
     * This is useful for detecting if the request came from an AJAX
     * script. Most of popular javascript libraries send this header.
     *
     * @return bool TRUE if the header exists and the value is as
     *         expected, FALSE otherwise.
     *
     */
    public function isXMLHTTPRequest()
    {
        return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }
    
    /**
     * Check if the request is using HTTPS protocol.
     * 
     * Checks that $_SERVER['HTTPS'] exists, not empty, and contains
     * something other than 'off'.
     *
     * Credits to the fantastic Joomla team, this snippet of code is
     * taken directly from their URI class.
     * 
     * @return bool TRUE if the request is using HTTPS, FALSE
     *         otherwise.
     *
     */
    public function isHTTPS()
    {
        $https = $this->getServer('HTTPS');
        return (
            isset($https) &&
            !empty($https) &&
            strtolower($https) != 'off'
        );
    }
    
    /**
     * Check if the request is coming from CLI.
     * 
     * Checks it using php_sapi_name() and the contents of
     * $_SERVER['REMOTE_ADDR'].
     * 
     * @return bool TRUE if the request is coming from a CLI
     *         environment, FALSE otherwise.
     *
     */
    public function isCLI()
    {
        $remoteAddr = $this->getServer('REMOTE_ADDR');
        return (
            strtolower(php_sapi_name()) == 'cli' AND
            empty($remoteAddr)
        );
    }
    
    /**
     * Accesses the wrapped $_SERVER variable.
     *
     * @param string $index The index of the value to be returned.
     *        Defaults to NULL, which returns the whole array.
     * @param mixed $default Default value to be returned if the
     *        index given does not exist. Defaults to NULL.
     * @return mixed
     *
     */
    public function getServer($index = NULL, $default = NULL)
    {
        if ($index == NULL)
        {
            return $this->server;
        }
        
        if (array_key_exists($index, $this->server))
        {
            return $this->server[$index];
        }
        
        return $default;
    }
    
    /**
     * Accesses the wrapped $_GET variable.
     *
     * @param string $index The index of the value to be returned.
     *        Defaults to NULL, which returns the whole array.
     * @param mixed $default Default value to be returned if the
     *        index given does not exist. Defaults to NULL.
     * @return mixed
     *
     */
    public function getGet($index = NULL, $default = NULL)
    {
        if ($index == NULL)
        {
            return $this->get;
        }
        
        if (array_key_exists($index, $this->get))
        {
            return $this->get[$index];
        }
        
        return $default;
    }
    
    /**
     * Accesses the wrapped $_POST variable.
     *
     * @param string $index The index of the value to be returned.
     *        Defaults to NULL, which returns the whole array.
     * @param mixed $default Default value to be returned if the
     *        index given does not exist. Defaults to NULL.
     * @return mixed
     *
     */
    public function getPost($index = NULL, $default = NULL)
    {
        if ($index == NULL)
        {
            return $this->post;
        }
        
        if (array_key_exists($index, $this->post))
        {
            return $this->post[$index];
        }
        
        return $default;
    }
    
    /**
     * Accesses the wrapped $_FILES variable.
     *
     * @param string $index The index of the value to be returned.
     *        Defaults to NULL, which returns the whole array.
     * @param mixed $default Default value to be returned if the
     *        index given does not exist. Defaults to NULL.
     * @return mixed
     *
     */
    public function getFiles($index = NULL, $default = NULL)
    {
        if ($index == NULL)
        {
            return $this->files;
        }
        
        if (array_key_exists($index, $this->files))
        {
            return $this->files[$index];
        }
        
        return $default;
    }
    
    /**
     * Accesses the wrapped $_COOKIE variable.
     *
     * @param string $index The index of the value to be returned.
     *        Defaults to NULL, which returns the whole array.
     * @param mixed $default Default value to be returned if the
     *        index given does not exist. Defaults to NULL.
     * @return mixed
     *
     */
    public function getCookie($index = NULL, $default = NULL)
    {
        if ($index == NULL)
        {
            return $this->cookie;
        }
        
        if (array_key_exists($index, $this->cookie))
        {
            return $this->cookie[$index];
        }
        
        return $default;
    }
    
    /**
     * Accesses the wrapped $_REQUEST variable.
     *
     * @param string $index The index of the value to be returned.
     *        Defaults to NULL, which returns the whole array.
     * @param mixed $default Default value to be returned if the
     *        index given does not exist. Defaults to NULL.
     * @return mixed
     *
     */
    public function getRequest($index = NULL, $default = NULL)
    {
        if ($index == NULL)
        {
            return $this->request;
        }
        
        if (array_key_exists($index, $this->request))
        {
            return $this->request[$index];
        }
        
        return $default;
    }
    
    /**
     * Accesses the request header(s).
     *
     * This method is case insensitive in handling the given index.
     * This is because RFC2616 (HTTP/1.1) defines header fields as
     * case insensitive.
     * 
     * @param string $index The index of the value to be returned.
     *        Defaults to NULL, which returns the whole array.
     * @param mixed $default Default value to be returned if the
     *        index given does not exist. Defaults to NULL.
     * @return mixed
     *
     */
    public function getHeader($index = NULL, $default = NULL)
    {
        if ($index == NULL)
        {
            return $this->header;
        }
        
        $index = strtoupper($index);
        
        if (array_key_exists($index, $this->header))
        {
            return $this->header[$index];
        }
        
        return $default;
    }
    
    /**
     * Wrapper for PHP isset() on $_SERVER array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetServer($index)
    {
        return isset($this->server[$index]);
    }
    
    /**
     * Wrapper for PHP isset() on $_GET array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetGet($index)
    {
        return isset($this->get[$index]);
    }
    
    /**
     * Wrapper for PHP isset() on $_POST array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetPost($index)
    {
        return isset($this->post[$index]);
    }
    
    /**
     * Wrapper for PHP isset() on $_FILES array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetFiles($index)
    {
        return isset($this->files[$index]);
    }
    
    /**
     * Wrapper for PHP isset() on $_COOKIE array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetCookie($index)
    {
        return isset($this->cookie[$index]);
    }
    
    /**
     * Wrapper for PHP isset() on $_REQUEST index.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetRequest($index)
    {
        return isset($this->request[$index]);
    }
    
    /**
     * Wrapper for PHP isset() on $_ENV array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetEnv($index)
    {
        return isset($this->env[$index]);
    }
    
    /**
     * Wrapper for PHP isset() on headers array.
     * 
     * This method is case insensitive in handling the given index.
     * This is because RFC2616 (HTTP/1.1) defines header fields as
     * case insensitive.
     * 
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetHeader($index)
    {
        return isset($this->headers[$index]);
    }
    
    /**
     * Initializes the headers array.
     * 
     * Loops through $_SERVER and converts every variable with the
     * prefix 'HTTP_' into a header item. This is because in nginx,
     * headers are usually converted into 'HTTP_*' style strings.
     *
     * Afterwards it tries to call apache_request_headers(). The
     * value from this function overwrites previously set header
     * items.
     *
     * Because RFC2616 (HTTP/1.1) defines header fields as case
     * insensitive, indexes of the header arrays are transformed to
     * uppercase first.
     *
     * @see __construct()
     *
     */
    protected function initializeHeaders()
    {
        $this->headers = array();
        
        foreach ($this->server as $key => $value)
        {
            if (substr($key, 0, 5) == 'HTTP_')
            {
                $headerKey = str_replace('_', '-', substr($key, 5));
                $headerKey = strtoupper($headerKey);
                $this->server[$headerKey] = $value;
            }
        }
        
        if (!function_exists('apache_request_headers'))
        {
            return;
        }
        
        $apacheHeaders = apache_request_headers();
        
        if ($apacheHeaders == FALSE)
        {
            return;
        }
        
        foreach ($apacheHeaders as $key => $value)
        {
            $key = strtoupper($key);
            $this->headers[$key] = $value;
        }
    }
}