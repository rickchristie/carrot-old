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
 * This is a value object that represents server's response to a
 * request. It encapsulates the response headers and body to be
 * sent back to the client. Headers and body are saved first and
 * not sent until the send() method is called. To properly make
 * use of this object you must not print or echo content to the
 * client directly, save the content into a string and inject
 * it to this object.
 *
 * <code>
 * $response = new Response('<h1>This is the content!</h1>');
 * </code>
 *
 * You can then append the response body:
 *
 * <code>
 * $response->appendBody('<p>This is the paragraph!</p>');
 * </code>
 *
 * You can also reset the response body:
 *
 * <code>
 * $response->setBody('<p>Starting from scratch!</p>');
 * </code>
 * 
 * Server protocol defaults to 'HTTP/1.0'. You can set server
 * protocol directly:
 * 
 * <code>
 * $response->setServerProtocol('HTTP/1.1');
 * </code>
 * 
 * When you use Carrot, you don't have to set server protocol
 * manually since the front controller or the exception handler
 * manager will set the default server protocol to match the
 * protocol in $_SERVER['SERVER_PROTOCOL'].
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
     * @var string The protocol to be written in header when returning status codes.
     */
    protected $serverProtocol;
    
    /**
     * @var string The default server protocol to be used in case server protocol was not set.
     */
    protected $defaultServerProtocol = 'HTTP/1.0';
    
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
     *
     */
    public function __construct($body = '', $statusCode = 200)
    {
        $this->body = $body;
        $this->setStatus($statusCode);
    }
    
    /**
     * Sets the server protocol.
     * 
     * @param string $serverProtocol Used when setting the response code, either 'HTTP/1.0' or 'HTTP/1.1'.
     * 
     */
    public function setServerProtocol($serverProtocol)
    {   
        $this->serverProtocol = $serverProtocol;
    }
    
    /**
     * Sets the default server protocol.
     * 
     * The default server protocol will only be used if the server
     * protocol is not set. This method exists so that the front
     * controller can set the default server protocol of the returned
     * response without changing the server protocol if it has already
     * been set by the user.
     * 
     * @param string $defaultServerProtocol Used when setting the response code, either 'HTTP/1.0' or 'HTTP/1.1'.
     *
     */
    public function setDefaultServerProtocol($defaultServerProtocol)
    {   
        $this->defaultServerProtocol = $defaultServerProtocol;
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
     * Please note that if the headers are already sent, this method
     * will not try to send the headers.
     *
     */
    public function send()
    {
        $this->sendHeaders();
        echo $this->body;
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
    
    /**
     * Sends the headers, including the status code.
     *
     * Loops through the added headers and sends them using header().
     * This method will not send the header if headers are already
     * sent.
     *
     */
    protected function sendHeaders()
    {
        if (!headers_sent())
        {
            $serverProtocol = $this->getServerProtocol();
            header("{$serverProtocol} {$this->statusCode} {$this->statusMessage}");
        
            foreach ($this->headers as $headerName => $contents)
            {
                header("{$headerName}: {$contents}");
            }
        }
    }
    
    /**
     * Gets the server protocol.
     *
     * If the server protocol is not set, will return the default
     * server protocol.
     *
     * @return string The server protocol.
     *
     */
    protected function getServerProtocol()
    {
        if (!empty($this->serverProtocol))
        {
            return $this->serverProtocol;
        }
        
        return $this->defaultServerProtocol;
    }
}