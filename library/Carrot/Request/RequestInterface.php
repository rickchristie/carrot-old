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
 * Request interface.
 *
 * The request object provides access to request environment
 * variables, wraps PHP superglobals and provides helper methods
 * to inspect the nature of the request. This object MUST be
 * immutable.
 *
 * This interface defines the contract between the request object
 * implementation and Carrot's core classes.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Request;

interface RequestInterface
{   
    /**
     * Checks if the current request is a GET request.
     *
     * @return bool TRUE if a GET request, FALSE otherwise.
     *
     */
    public function isGet();
    
    /**
     * Checks if the current request is a POST request.
     *
     * @return bool TRUE if a POST request, FALSE otherwise.
     *
     */
    public function isPost();
    
    /**
     * Checks if the current request is a PUT request.
     *
     * @return bool TRUE if a PUT request, FALSE otherwise.
     *
     */
    public function isPut();
    
    /**
     * Checks if the current request is a DELETE request.
     *
     * @return bool TRUE if a DELETE request, FALSE otherwise.
     *
     */
    public function isDelete();
    
    /**
     * Checks if the current request is a HEAD request.
     *
     * @return bool TRUE if a HEAD request, FALSE otherwise.
     *
     */
    public function isHead();
    
    /**
     * Checks if the current request is a OPTIONS request.
     *
     * @return bool TRUE if an OPTIONS request, FALSE otherwise.
     *
     */
    public function isOptions();
    
    /**
     * Check if the header X_REQUESTED_WITH exists and its value is
     * 'XMLHttpRequest'.
     *
     * @return bool TRUE if the header exists and the value is as
     *         expected, FALSE otherwise.
     *
     */
    public function isXMLHTTPRequest();
    
    /**
     * Check if the request is using HTTPS protocol.
     * 
     * @return bool TRUE if the request is using HTTPS, FALSE
     *         otherwise.
     *
     */
    public function isHTTPS();
    
    /**
     * Check if the request is coming from CLI.
     * 
     * @return bool TRUE if the request is coming from a CLI
     *         environment, FALSE otherwise.
     *
     */
    public function isCLI();
    
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
    public function getServer($index = NULL, $default = NULL);
    
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
    public function getGet($index = NULL, $default = NULL);
    
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
    public function getPost($index = NULL, $default = NULL);
    
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
    public function getFiles($index = NULL, $default = NULL);
    
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
    public function getCookie($index = NULL, $default = NULL);
    
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
    public function getRequest($index = NULL, $default = NULL);
    
    /**
     * Accesses the request header(s).
     * 
     * This method must be case insensitive in handling the given
     * index. This is because RFC2616 (HTTP/1.1) defines header
     * fields as case insensitive.
     * 
     * @param string $index The index of the value to be returned.
     *        Defaults to NULL, which returns the whole array.
     * @param mixed $default Default value to be returned if the
     *        index given does not exist. Defaults to NULL.
     * @return mixed
     *
     */
    public function getHeader($index = NULL, $default = NULL);
    
    /**
     * Wrapper for PHP isset() on $_SERVER array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetServer($index);
    
    /**
     * Wrapper for PHP isset() on $_GET array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetGet($index);
    
    /**
     * Wrapper for PHP isset() on $_POST array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetPost($index);
    
    /**
     * Wrapper for PHP isset() on $_FILES array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetFiles($index);
    
    /**
     * Wrapper for PHP isset() on $_COOKIE array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetCookie($index);
    
    /**
     * Wrapper for PHP isset() on $_REQUEST index.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetRequest($index);
    
    /**
     * Wrapper for PHP isset() on $_ENV array.
     *
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetEnv($index);
    
    /**
     * Wrapper for PHP isset() on headers array.
     * 
     * This method must be case insensitive in handling the given
     * index. This is because RFC2616 (HTTP/1.1) defines header
     * fields as case insensitive.
     * 
     * @param string $index The index to check.
     * @return bool
     *
     */
    public function issetHeader($index);
}