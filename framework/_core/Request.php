<?php

/**
 * Request
 *
 * Licensed under the MIT License.
 *
 * Represents the actual request to the server. Accepts and stores $_SERVER,
 * $_GET, $_POST, $_FILES, $_COOKIE, $_REQUEST, $_ENV. Supports HTTP/HTTPS
 * protocol only. This class assumes that the main request handler is always
 * named 'index.php'.
 *
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 */

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
	 * @var array List of generated attributes, things like 'domain_name', 'base_path', etc. For information on individual attributes, see get methods.
	 */
	protected $attributes;
	
	/**
	 * 
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
		
		$this->attributes['url_protocol'] = strtolower(substr($this->server('SERVER_PROTOCOL'), 0, 5)) == 'https' ? 'https' : 'http';
		$this->attributes['domain_name'] = $this->server('SERVER_NAME');
		$this->attributes['base_path'] = $this->generate_base_path();
		$this->attributes['application_request_uri'] = $this->generate_application_request_uri();
	}
	
	/**
	 * Returns wrapped $_SERVER variable.
	 *
	 * @param string $item Leave empty to return the whole array.
	 * @return mixed
	 *
	 */
	public function server($item = '')
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
	public function get($item = '')
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
	public function post($item = '')
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
	public function files($item = '')
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
	public function cookie($item = '')
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
	public function request($item = '')
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
	public function env($item = '')
	{
		if (empty($item))
		{
			return $this->env;
		}
		
		return $this->env[$item];
	}
	
	/**
	 * Returns an array of generated request attributes.
	 *
	 * @param string $item Leave empty to return the whole array.
	 * @return mixed
	 *
	 */
	public function attribute($item = '')
	{
		if (empty($item))
		{
			return $this->attributes;
		}
		
		return $this->attributes[$item];
	}
	
	// ---------------------------------------------------------------
	
	/**
	 * Returns base URL.
	 *
	 * @return string Base URL, with a trailing slash.
	 *
	 */
	public function get_base_url()
	{
		return $this->attributes['url_protocol'] . '://' . $this->attributes['domain_name'] . $this->attributes['base_path'];
	}
	
	/**
	 * Returns domain name.
	 *
	 * @return string Domain name, without protocol and trailing slash.
	 *
	 */
	public function get_domain_name()
	{
		return $this->attributes['domain_name'];
	}
	
	/**
	 * Returns base path.
	 *
	 * Base path is the path from server root where this request is
	 * executed. Note that this Request class assumes that the request
	 * handler is ALWAYS named 'index.php'. Examples of base paths with
	 * regarding the location of their request handler.
	 *
	 * Base paths always have starting and trailing slash.
	 *
	 * <code>
	 * http://localhost/index.php will return  /
	 * http://localhost/carrot/index.php will return /carrot/
	 * </code>
	 *
	 * @return string Path from root to the request handler.
	 *
	 */
	public function get_base_path()
	{
		return $this->attributes['base_path'];
	}
	
	/**
	 * Returns short server protocol string. Useful when constructing URL.
	 *
	 * @return string Either 'http' or 'https'.
	 *
	 */
	public function get_url_protocol()
	{
		return $this->attributes['url_protocol'];
	}
	
	/**
	 * Creates an instance of Application_Request_URI.
	 *
	 * 
	 *
	 */
	public function get_application_request_uri()
	{
		
	}
	
	// ---------------------------------------------------------------
	
	/**
	 * Generates base path.
	 *
	 * Assuming that each request will be handled by the main 'index.php'
	 * file, we will use SCRIPT_NAME to get the path from root to the
	 * request handler.
	 * 
	 * Base path always have starting and trailing slash.
	 *
	 * @return string Path from root to the request handler.
	 *
	 */
	protected function generate_base_path()
	{
		$path = str_ireplace('/index.php', '', $this->server('SCRIPT_NAME'));
		
		// Add trailing slash to default path, if it doesn't have it
		if (empty($path) or substr($path, -1) != '/')
		{
			$path .= '/';
		}
		
		return $path;
	}
	
	
	protected function generate_application_request_uri()
	{
		
	}
}