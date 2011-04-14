<?php

/**
 * Response object.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package			Carrot
 * @author 		  	Ricky Christie <seven.rchristie@gmail.com>
 * @copyright		2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license			http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		 	0.1
 * @version			0.1
 */

/**
 * Response object.
 *
 * The object representing the framework's response to a particular request. It
 * doesn't support anything besides than HTTP 1.0 and HTTP 1.1 protocol.
 *
 * @package			Carrot
 * @author 		  	Ricky Christie <seven.rchristie@gmail.com>
 * @copyright		2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license			http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		 	0.1
 * @version			0.1
 * @todo			Make an abstract version so you can split it to HTTP 1.0, HTTP 1.1, WebSocket, etc
 */

class Response
{
	/**
	 * @var string Array containing the headers set by user.
	 */
	protected $headers = array();
	
	/**
	 * @var string A string containing the request body.
	 */
	protected $body = '';
	
	/**
	 * @var int Variable containing the status code. Defaults to 200 (OK).
	 */
	protected $status = 200;
	
	/**
	 * @var array Array containing the list of status codes and their default message.
	 */
	protected $status_codes = array
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
	 * @var array List of allowed response header fields for HTTP 1.0.
	 */
	protected $header_fields_1_0 = array
	(
		'Allow',
		'Content-Language',
		'Content-Encoding',
		'Content-Length',
		'Content-Type',
		'Date',
		'Expires',
		'Last-Modified',
		'Link',
		'Location',
		'Pragma',
		'Retry-After',
		'Server',
		'WWW-Authenticate'
	);
	
	/**
	 * @var array List of allowed response header fields for HTTP 1.1.
	 */
	protected $header_fields_1_1 = array
	(
		'Accept-Ranges',
		'Age',
		'Allow',
		'Cache-Control',
		'Content-Encoding',
		'Content-Language',
		'Content-Length',
		'Content-Location',
		'Content-MD5',
		'Content-Disposition',
		'Content-Range',
		'Content-Type',
		'Date',
		'Etag',
		'Expires',
		'Last-Modified',
		'Link',
		'Location',
		'Pragma',
		'Proxy-Authenticate',
		'Retry-After',
		'Server',
		'Trailer',
		'Transfer-Encoding',
		'Vary',
		'Via',
		'Warning',
		'WWW-Authenticate'
	);
	
	/**
	 * @var array Array containing the allowed header fields, set at constructor.
	 */
	protected $header_fields;
	
	/**
	 * @var array The protocol to be written in header when returning status codes.
	 */
	protected $server_protocol;
	
	/**
	 * @var string The current status code.
	 */
	protected $status_code = NULL;
	
	/**
	 * Creates the request object.
	 *
	 * This request object assumes that output buffering has
	 * already started. Therefore it doesn't check if headers
	 * are sent. This class only supports HTTP/1.0 and HTTP/1.1
	 * protocol, anything other than that and it will throw
	 * InvalidArgumentException.
	 *
	 * @param string $server_protocol Must be either 'HTTP/1.1' or 'HTTP/1.0'.
	 * @throws InvalidArgumentException
	 *
	 */
	public function __construct($server_protocol)
	{
		// Fill in header fields based on which protocol we are at.
		if ($server_protocol == 'HTTP/1.0')
		{
			$this->server_protocol = $server_protocol;
			$this->header_fields = $this->header_fields_1_0;
			return;
		}
		
		if ($server_protocol == 'HTTP/1.1')
		{
			$this->server_protocol = $server_protocol;
			$this->header_fields = $this->header_fields_1_1;
			return;
		}
		
		throw new InvalidArgumentException('Error instantiating Response object. Server protocol must be HTTP/1.0 or HTTP/1.1.');
	}
	
	/**
	 * Sets the header.
	 *
	 * Sets the header using header() and writes a
	 * record of it in $this->headers. You cannot
	 * set the status code using this method. Only
	 * regular headers are supported. It automatically
	 * adds the colon (:) for you.
	 *
	 * <code>$response->set_header('Content-Type', 'text/html');</code>
	 *
	 * Will result in:
	 *
	 * <code>header('Content-Type: text/html');</code>
	 *
	 * This method also checks if the header field is allowed
	 * for the HTTP version you are using as $this->server_protocol.
	 * To skip the check, use $force optional parameter.
	 *
	 * @param string $header_name
	 * @param string $contents
	 * @param bool $force Optional. Use TRUE to skip header field validation. Defaults to FALSE.
	 * @return bool TRUE if successful, FALSE if otherwise.
	 *
	 */
	public function set_header($header_name, $contents, $force = FALSE)
	{
		// Checks if $header_name is allowed
		if (!$force && !in_array($header_name, $this->header_fields))
		{
			return FALSE;
		}
		
		if (!headers_sent())
		{
			$this->headers[$header_name] = $contents;
			header("{$header_name}: {$contents}");
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Removes a header or all previously set headers.
	 *
	 * This is a wrapper for header_remove(). Other
	 * than also removing the actual header, it 
	 * also removes the record in $this->headers.
	 *
	 * @param string $header_name If not specified, all previously set headers will be removed.
	 * @return bool TRUE if headers are not sent yet, FALSE if otherwise.
	 *
	 */
	public function remove_header($header_name = '')
	{
		if (!headers_sent())
		{
			// Remove all headers
			if (empty($header_name))
			{
				header_remove();
				$this->headers = array();
				return TRUE;
			}
			
			// Remove one particular header
			header_remove($header_name);
			unset($this->headers[$header_name]);
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Sets the status code.
	 *
	 * Checks if the status code is valid. If custom
	 * message are not set, default message will be
	 * used instead. If the status code is invalid it
	 * will simply return FALSE. This method also
	 * records the status code sent in $this->status_code.
	 *
	 * @param int $code HTTP status code.
	 * @param string $message Message to accompany the status code.
	 * @return bool TRUE if successful, FALSE if not.
	 *
	 */
	public function set_status($code, $message = '')
	{	
		if (isset($this->status_codes[$code]))
		{
			if (empty($message))
			{
				$message = $this->status_codes[$code];
			}
			
			$this->set_header("{$this->server_protocol} {$code} {$message}");
			$this->status_code = $code;
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Sends a quick redirection response header.
	 * 
	 * This method automatically clears all the
	 * headers. If headers are sent already, it
	 * simply returns FALSE. It doesn't exit the
	 * PHP processing for you, so you can still
	 * do some processing after we sent the
	 * redirection header.
	 *
	 * @param string $location The URL.
	 * @return bool TRUE on success, FALSE of failure.
	 *
	 */
	public function redirect($location)
	{
		if (!headers_sent())
		{
			$this->remove_header();
			$this->set_header('Location', $location);
			return TRUE;
		}
		
		return FALSE;
	}
	
	// ---------------------------------------------------------------
	
	public function get_body()
	{
		return $this->body;
	}
	
	public function set_body($string)
	{
		$this->body = $string;
	}
	
	public function append_body($string)
	{
		$this->body .= $string;
	}
	
	public function get_header()
	{
		return $this->headers;
	}
}