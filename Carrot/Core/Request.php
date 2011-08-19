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
 * Request
 * 
 * Carrot's default Request class, represents an actual request to
 * the server. Accepts and stores $_SERVER, $_GET, $_POST,
 * $_FILES, $_COOKIE, $_REQUEST, and $_ENV as immutable arrays.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class Request
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
    public function isGetRequest()
    {
        return (strtoupper($this->getServer('REQUEST_METHOD')) == 'GET');
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
    public function isPostRequest()
    {
        return (strtoupper($this->getServer('REQUEST_METHOD')) == 'POST');
    }
    
    /**
     * Returns wrapped $_SERVER variable.
     *
     * @param string $index Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getServer($index = '')
    {   
        if (empty($index))
        {
            return $this->server;
        }
        
        if (!array_key_exists($index, $this->server))
        {
            return NULL;
        }
        
        return $this->server[$index];
    }
    
    /**
     * Returns wrapped $_GET variable.
     *
     * @param string $index Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getGet($index = '')
    {
        if (empty($index))
        {
            return $this->get;
        }
        
        if (!array_key_exists($index, $this->get))
        {
            return NULL;
        }
        
        return $this->get[$index];
    }
    
    /**
     * Returns wrapped $_POST variable.
     *
     * @param string $index Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getPost($index = '')
    {
        if (empty($index))
        {
            return $this->post;
        }
        
        if (!array_key_exists($index, $this->post))
        {
            return NULL;
        }
        
        return $this->post[$index];
    }
    
    /**
     * Returns wrapped $_FILES variable.
     *
     * @param string $index Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getFiles($index = '')
    {
        if (empty($index))
        {
            return $this->files;
        }
        
        if (!array_key_exists($index, $this->files))
        {
            return NULL;
        }
        
        return $this->files[$index];
    }
    
    /**
     * Returns wrapped $_COOKIE variable.
     *
     * @param string $index Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getCookie($index = '')
    {
        if (empty($index))
        {
            return $this->cookie;
        }
        
        if (!array_key_exists($index, $this->cookie))
        {
            return NULL;
        }
        
        return $this->cookie[$index];
    }
    
    /**
     * Returns wrapped $_REQUEST variable.
     *
     * @param string $index Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getRequest($index = '')
    {
        if (empty($index))
        {
            return $this->request;
        }
        
        if (!array_key_exists($index, $this->request))
        {
            return NULL;
        }
        
        return $this->request[$index];
    }
    
    /**
     * Returns wrapped $_ENV variable.
     *
     * @param string $index Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getEnv($index = '')
    {
        if (empty($index))
        {
            return $this->env;
        }
        
        if (!array_key_exists($index, $this->env))
        {
            return NULL;
        }
        
        return $this->env[$index];
    }
    
    /**
     * Returns the $_SERVER variable or the default value if it doesn't exist.
     * 
     * @param string $index The index of the value to get.
     * @param mixed $default The default value to be returned.
     *
     */
    public function getServerOrReturnDefault($index, $default)
    {
        if (array_key_exists($index, $this->server))
        {
            return $this->server[$index];
        }
        
        return $default;
    }
    
    /**
     * Returns the $_GET variable or the default value if it doesn't exist.
     * 
     * @param string $index The index of the value to get.
     * @param mixed $default The default value to be returned.
     *
     */
    public function getGetOrReturnDefault($index, $default)
    {
        if (array_key_exists($index, $this->get))
        {
            return $this->get[$index];
        }
        
        return $default;
    }
    
    /**
     * Returns the $_POST variable or the default value if it doesn't exist.
     * 
     * @param string $index The index of the value to get.
     * @param mixed $default The default value to be returned.
     *
     */
    public function getPostOrReturnDefault($index, $default)
    {
        if (array_key_exists($index, $this->post))
        {
            return $this->post[$index];
        }
        
        return $default;
    }
    
    /**
     * Returns the $_FILES variable or the default value if it doesn't exist.
     * 
     * @param string $index The index of the value to get.
     * @param mixed $default The default value to be returned.
     *
     */
    public function getFilesOrReturnDefault($index, $default)
    {
        if (array_key_exists($index, $this->files))
        {
            return $this->files[$index];
        }
        
        return $default;
    }
    
    /**
     * Returns the $_COOKIE variable or the default value if it doesn't exist.
     * 
     * @param string $index The index of the value to get.
     * @param mixed $default The default value to be returned.
     *
     */
    public function getCookieOrReturnDefault($index, $default)
    {
        if (array_key_exists($index, $this->cookie))
        {
            return $this->cookie[$index];
        }
        
        return $default;
    }
    
    /**
     * Returns the $_REQUEST variable or the default value if it doesn't exist.
     * 
     * @param string $index The index of the value to get.
     * @param mixed $default The default value to be returned.
     *
     */
    public function getRequestOrReturnDefault($index, $default)
    {
        if (array_key_exists($index, $this->request))
        {
            return $this->request[$index];
        }
        
        return $default;
    }
    
    /**
     * Returns the $_ENV variable or the default value if it doesn't exist.
     * 
     * @param string $index The index of the value to get.
     * @param mixed $default The default value to be returned.
     *
     */
    public function getEnvOrReturnDefault($index, $default)
    {
        if (array_key_exists($index, $this->env))
        {
            return $this->env[$index];
        }
        
        return $default;
    }
    
    /**
     * Wrapper for PHP isset on $_SERVER index.
     *
     * @param string $index The index to check.
     * @return bool TRUE if the index exists, FALSE otherwise.
     *
     */
    public function issetServer($index)
    {
        return isset($this->server[$index]);
    }
    
    /**
     * Wrapper for PHP isset on $_GET index.
     *
     * @param string $index The index to check.
     * @return bool TRUE if the index exists, FALSE otherwise.
     *
     */
    public function issetGet($index)
    {
        return isset($this->get[$index]);
    }
    
    /**
     * Wrapper for PHP isset on $_POST index.
     *
     * @param string $index The index to check.
     * @return bool TRUE if the index exists, FALSE otherwise.
     *
     */
    public function issetPost($index)
    {
        return isset($this->post[$index]);
    }
    
    /**
     * Wrapper for PHP isset on $_FILES index.
     *
     * @param string $index The index to check.
     * @return bool TRUE if the index exists, FALSE otherwise.
     *
     */
    public function issetFiles($index)
    {
        return isset($this->files[$index]);
    }
    
    /**
     * Wrapper for PHP isset on $_COOKIE index.
     *
     * @param string $index The index to check.
     * @return bool TRUE if the index exists, FALSE otherwise.
     *
     */
    public function issetCookie($index = '')
    {
        return isset($this->cookie[$index]);
    }
    
    /**
     * Wrapper for PHP isset on $_REQUEST index.
     *
     * @param string $index The index to check.
     * @return bool TRUE if the index exists, FALSE otherwise.
     *
     */
    public function issetRequest($index = '')
    {
        return isset($this->request[$index]);
    }
    
    /**
     * Wrapper for PHP isset on $_ENV index.
     *
     * @param string $index The index to check.
     * @return bool TRUE if the index exists, FALSE otherwise.
     *
     */
    public function issetEnv($index = '')
    {
        return isset($this->env[$index]);
    }
}