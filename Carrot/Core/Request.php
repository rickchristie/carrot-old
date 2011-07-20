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
 * Carrot's default Request class, represents an actual request to the server.
 * Accepts and stores $_SERVER, $_GET, $_POST, $_FILES, $_COOKIE, $_REQUEST,
 * and $_ENV as immutable arrays.
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
     * Returns wrapped $_SERVER variable.
     *
     * @param string $item Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getServer($item = '')
    {
        if (empty($item))
        {
            return $this->server;
        }
        
        return $this->server[$item];
    }
    
    /**
     * Returns wrapped $_GET variable.
     *
     * @param string $item Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getGet($item = '')
    {
        if (empty($item))
        {
            return $this->get;
        }
        
        return $this->get[$item];
    }
    
    /**
     * Returns wrapped $_POST variable.
     *
     * @param string $item Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getPost($item = '')
    {
        if (empty($item))
        {
            return $this->post;
        }
        
        return $this->post[$item];
    }
    
    /**
     * Returns wrapped $_FILES variable.
     *
     * @param string $item Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getFiles($item = '')
    {
        if (empty($item))
        {
            return $this->files;
        }
        
        return $this->files[$item];
    }
    
    /**
     * Returns wrapped $_COOKIE variable.
     *
     * @param string $item Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getCookie($item = '')
    {
        if (empty($item))
        {
            return $this->cookie;
        }
        
        return $this->cookie[$item];
    }
    
    /**
     * Returns wrapped $_REQUEST variable.
     *
     * @param string $item Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getRequest($item = '')
    {
        if (empty($item))
        {
            return $this->request;
        }
        
        return $this->request[$item];
    }
    
    /**
     * Returns wrapped $_ENV variable.
     *
     * @param string $item Leave empty to return the whole array.
     * @return mixed
     *
     */
    public function getEnv($item = '')
    {
        if (empty($item))
        {
            return $this->env;
        }
        
        return $this->env[$item];
    }
    
    /**
     * Wrapper for PHP isset on $_SERVER index.
     *
     * @param string $index The index to check.
     * @return bool True if the index exists, false otherwise.
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
     * @return bool True if the index exists, false otherwise.
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
     * @return bool True if the index exists, false otherwise.
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
     * @return bool True if the index exists, false otherwise.
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
     * @return bool True if the index exists, false otherwise.
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
     * @return bool True if the index exists, false otherwise.
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
     * @return bool True if the index exists, false otherwise.
     *
     */
    public function issetEnv($index = '')
    {
        return isset($this->env[$index]);
    }
}