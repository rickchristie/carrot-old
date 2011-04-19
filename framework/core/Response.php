<?php

/**
 * Response
 *
 * Licensed under the MIT License.
 *
 * Represents the response of the controller. Views and controllers can build their
 * responses, which then can be appended on each other and returned to be examined
 * or sent. Supports HTTP/1.0 and HTTP/1.1 only.
 *
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 */

class Response
{
	/**
	 * @var array List of headers set by the user.
	 */
	protected $headers = array();
	
	/**
	 * @var array List of sent headers, grabbed using headers_list().
	 */
	protected $headers_list = array();
	
	/**
	 * @var string A string containing the request body.
	 */
	protected $body = '';
	
	/**
	 * @var int Variable containing the status code. Defaults to 200 (OK).
	 */
	protected $status = '';
	
	/**
	 * @var array The protocol to be written in header when returning status codes.
	 */
	protected $server_protocol;
	
	/**
	 * @var bool TRUE if Response:send() is called, FALSE otherwise.
	 */
	protected $sent = FALSE;
	
	/**
	 * @var array Array containing the list of status codes and their default message.
	 */
	protected $status_code_messages = array
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
	
	
	public function __construct($server_protocol)
	{
		if (!in_array($server_protocol, array('HTTP/1.0', 'HTTP/1.1')))
		{
			throw new InvalidArgumentException('Error in instantiating Response. Server protocol must be HTTP/1.0 or HTTP/1.1.');
		}
		
		$this->server_protocol = $server_protocol;
	}
	
	/**
	 * Sets the header.
	 *
	 * Sets the header using header() and writes a record of it in
	 * $this->headers. You cannot set the status code using this method.
	 * Only regular headers can be set with this method. It automatically
	 * adds the colon (:) for you.
	 *
	 * <code>$response->set_header('Content-Type', 'text/html');</code>
	 *
	 * Will result in:
	 *
	 * <code>header('Content-Type: text/html');</code>
	 *
	 * @param string $header_name
	 * @param string $contents
	 * @return bool TRUE if successful, FALSE if otherwise.
	 *
	 */
	public function set_header($header_name, $contents)
	{	
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
	 * This is a wrapper for header_remove(). Other than also removing
	 * the actual header, it also removes the record in $this->headers.
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
	 * Checks if the status code is valid. If custom message is not set, default message
	 * will be used instead. If the status code is invalid it will simply return FALSE.
	 * This method also records the status code set in $this->status_code.
	 *
	 * @param int $code HTTP status code.
	 * @param string $message Message to accompany the status code.
	 * @return bool TRUE if successful, FALSE if not.
	 *
	 */
	public function set_status($code, $message = '')
	{
		if (!headers_sent())
		{
			if (empty($message) && isset($this->status_code_messages[$code]))
			{
				$message = $this->status_code_messages[$code];
			}
			
			$code = intval($code);
			$this->status_code = $code;
			header("{$this->server_protocol} {$code} {$message}");
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Creates a quick redirection response.
	 * 
	 * This method automatically clears all the headers. If headers 
	 * are sent already, it simply returns FALSE. It doesn't exit the
	 * PHP processing for you, so you can still do some processing
	 * after we sent the redirection header.
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
	
	/**
	 * Sends the response to the client.
	 *
	 * Echoes out the body of the response, which also automatically
	 * sends the headers. Although this class already records each
	 * headers set by Response::set_header(), it doesn't record headers
	 * that are set directly by the user using PHP header() function -
	 * so it uses headers_list() to get the actual headers sent and
	 * stores them in Response::headers_list.
	 *
	 */
	public function send()
	{
		$this->headers_list = headers_list();
		$this->sent = TRUE;
		echo $this->body;
	}
		
	/**
	 * Appends a response class.
	 *
	 * This method appends a response body and overwrites the current
	 * headers with the new ones provided by the response object. Note
	 * that it assumes that Response::set_header() has been called already
	 * by the appended Response object, so it does nothing of the sort.
	 *
	 * @param Response $response Response object to be appended.
	 *
	 */
	public function append_response(Response $response)
	{
		$this->body .= $response->get_body();
		$new_headers = $response->get_header_records();
		
		foreach ($new_headers as $index => $contents)
		{
			$this->headers[$index] = $contents;
		}
	}
	
	/**
	 * Sets the body of the response.
	 *
	 * @param string $body Body of the response.
	 *
	 */
	public function set_body($body)
	{
		$this->body = $body;
	}
	
	/**
	 * Appends string to the response body.
	 *
	 * @param string $body_append String to be appended in the response body.
	 *
	 */
	public function append_body($body_append)
	{
		$this->body .= $body_append;
	}
	
	/**
	 * Returns the response body.
	 *
	 * @return string
	 *
	 */
	public function get_body()
	{
		return $this->body;
	}
	
	/**
	 * Returns the header records.
	 *
	 * @return array 
	 *
	 */
	public function get_header_records()
	{
		return $this->headers;
	}
}