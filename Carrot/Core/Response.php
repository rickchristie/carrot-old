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
 * Response
 * 
 * Carrot's response object. Currently it supports only HTTP/1.0
 * and HTTP/1.1. Server protocol defaults to HTTP/1.0. The
 * response object is a value object. So feel free to create it
 * anywhere you wish.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use InvalidArgumentException;

class Response
{
    /**
     * @var array List of headers set by the user.
     */
    protected $headers = array();
    
    /**
     * @var string A string containing the request body.
     */
    protected $body;
    
    /**
     * @var int Status code of the response.
     */
    protected $statusCode;
    
    /**
     * @var string Status message of the response.
     */
    protected $statusMessage;
    
    /**
     * @var array The protocol to be written in header when returning status codes.
     */
    protected $serverProtocol;
    
    /**
     * @var array Array containing the list of status codes and their default message.
     */
    protected $statusCodeMessages = array
    (
        // Class 1 - Informational
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Class 2 - Successful
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        
        // Class 3 - Redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy (No longer used)',
        307 => 'Temporary Redirect',
        
        // Class 4 - Client Error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        
        // Class 5 - Server Error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );
    
    /**
     * Constructs the response object.
     *
     * This constructor creates the default response object, it
     * automatically has 200 (OK) status code with HTTP/1.0 protocol.
     *
     * @param string $body The body of the response.
     * @param string $serverProtocol Used when setting the response code, either 'HTTP/1.0' or 'HTTP/1.1'. Defaults to HTTP/1.0.
     * @param int $code HTTP status code. Defaults to 200 (OK).
     *
     */
    public function __construct($body = '', $serverProtocol = 'HTTP/1.0', $code = 200)
    {
        $this->body = $body;
        $this->setProtocol($serverProtocol);
        $this->setStatus($code);
    }
    
    /**
     * Sets the server protocol.
     * 
     * @param string $serverProtocol Used when setting the response code, either 'HTTP/1.0' or 'HTTP/1.1'.
     * 
     */
    public function setProtocol($serverProtocol)
    {
        if (!in_array($serverProtocol, array('HTTP/1.0', 'HTTP/1.1')))
        {
            throw new InvalidArgumentException('Error in instantiating Response. Server protocol must be HTTP/1.0 or HTTP/1.1.');
        }
        
        $this->serverProtocol = $serverProtocol;
    }
    
    /**
     * Add a header to be sent later on.
     * 
     * Adds a header to the header records. This doesn't immediately
     * send the header, it will be sent when send() is called. You
     * can't set the status code with this method.
     *
     * <code>
     * $response->setHeader('Content-Type', 'text/html');
     * </code>
     *
     * @param string $headerName
     * @param string $contents
     *
     */
    public function addHeader($headerName, $contents)
    {
        $this->headers[$headerName] = $contents;
    }
    
    /**
     * Removes a previously added header in header records.
     *
     * It unsets the appropriate array in $headers class property.
     *
     * @param string $headerName Name of the header to be removed.
     *
     */
    public function removeHeader($headerName)
    {   
        unset($this->headers[$headerName]);
    }
    
    /**
     * Removes all previously added headers.
     *
     * This resets the header records into an empty array.
     *
     */
    public function removeAllHeaders()
    {
        $this->headers = array();
    }
    
    /**
     * Sets the status code.
     *
     * If custom message is not set, default message will be used
     * instead. Please note that this method does not send the status
     * code immediately, it will be sent when send() method is called.
     *
     * Will throw InvalidArgumentException if the status code is not
     * in the status code list.
     *
     * @throws InvalidArgumentException
     * @param int $code HTTP status code.
     * @param string $message Message to accompany the status code.
     *
     */
    public function setStatus($code, $message = '')
    {
        if (!array_key_exists($code, $this->statusCodeMessages))
        {
            throw new InvalidArgumentException("Response error in setting status code. Status code '{$code}' is not recognized.");
        }
        
        if (empty($message) && isset($this->statusCodeMessages[$code]))
        {
            $message = $this->statusCodeMessages[$code];
        }
        
        $code = intval($code);
        $this->statusCode = $code;
        $this->statusMessage = $message;
    }
    
    /**
     * Adds the redirection header.
     * 
     * This method will automatically clear all previously set
     * headers. It will then add the 'Location' header to the
     * response. Please note that this doesn't redirect immediately,
     * as you still have to return this object to the front controller
     * where the send() method will be called.
     *
     * @param string $location The URL.
     *
     */
    public function redirect($location)
    {
        $this->removeAllHeaders();
        $this->setHeader('Location', $location);
    }
    
    /**
     * Sends the response to the client.
     *
     * Sends the header and echoes out the body of the response.
     *
     */
    public function send()
    {
        $this->sendHeaders();
        echo $this->body;
    }
    
    /**
     * Sends the headers, including the status code.
     *
     * Loops through the added headers and sends them using header().
     *
     */
    protected function sendHeaders()
    {
        if (!headers_sent())
        {
            header("{$this->serverProtocol} {$this->statusCode} {$this->statusMessage}");
        
            foreach ($this->headers as $headerName => $contents)
            {
                header("{$headerName}: {$contents}");
            }
        }
    }
    
    /**
     * Sets the body of the response.
     *
     * @param string $body Body of the response.
     *
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
    
    /**
     * Appends string to the response body.
     *
     * @param string $bodyAppend String to be appended in the response body.
     *
     */
    public function appendBody($bodyAppend)
    {   
        $this->body .= $bodyAppend;
    }
    
    /**
     * Returns the response body.
     *
     * @return string
     *
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * Returns the header records.
     *
     * @return array 
     *
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}