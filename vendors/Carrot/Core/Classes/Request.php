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
 * Represents the actual request to the server. Accepts and stores $_SERVER,
 * $_GET, $_POST, $_FILES, $_COOKIE, $_REQUEST, $_ENV. Supports HTTP/HTTPS
 * protocol only. This class assumes that the main request handler is always
 * named 'index.php'.
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

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
	 * @var string Base path (relative path from the server root to the folder where the front controller/index.php file resides), with starting and trailing slash.
	 */
	protected $base_path;
	
	/**
	 * @var array Application request URI, segmented inside an array.
	 */
	protected $app_request_uri_segments;
	
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
	 * @param string $base_path Optional. Base path, with starting and trailing slash.
	 *
	 */
	public function __construct($server, $get, $post, $files, $cookie, $request, $env, $base_path = '')
	{
		$this->server = $server;
		$this->get = $get;
		$this->post = $post;
		$this->files = $files;
		$this->cookie = $cookie;
		$this->request = $request;
		$this->env = $env;
		
		if (empty($base_path))
		{
			$base_path = $this->guessBasePath();
		}
		
		$this->base_path = $base_path;
		$this->app_request_uri_segments = $this->generateAppRequestURISegments();
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
	
	// ---------------------------------------------------------------
	
	/**
	 * Returns base path (with starting and trailing slash).
	 *
	 * Base path is the relative path from server root to the folder
	 * where the front controller is located. If the front controller
	 * is in the server root, it simply returns '/'.
	 *
	 * @return string Base path.
	 *
	 */
	public function getBasePath()
	{
		return $this->base_path;
	}
	
	/**
	 * Returns the application URI segments (array).
	 *
	 * Application request URI is different from Request URI in that it doesn't
	 * include the base path. So if your base path is '/base/path/' and
	 * your the request uri is '/base/path/item/id', the application request
	 * URI will be:
	 *
	 * <code>
	 * array('item', 'id')
	 * </code>
	 *
	 * @param int $index Index of the segment to be returned, leave empty to return the whole array.
	 * @return mixed Either one segment or the entire segment array.
	 *
	 */
	public function getAppRequestURISegments($index = '')
	{
		if (empty($index))
		{
			return $this->app_request_uri_segments;
		}
		
		return $this->app_request_uri_segments[$index];
	}
	
	// ---------------------------------------------------------------
	
	/**
	 * Generates base path.
	 *
	 * Assuming that each request will be handled by the main 'index.php'
	 * file, we will use SCRIPT_NAME to get the path from root to the
	 * request handler. This method will be called if base path is not
	 * given in the constructor.
	 * 
	 * << WARNING >> - When you can, specify the base path manually -
	 * guessing the base path using SCRIPT_NAME is a security risk.
	 *
	 * Base path always have starting and trailing slash.
	 *
	 * @return string Path from root to the request handler.
	 *
	 */
	protected function guessBasePath()
	{
		$path = str_ireplace('/index.php', '', $this->server['SCRIPT_NAME']);
		
		// Add trailing slash to default path, if it doesn't have it
		if (empty($path) or substr($path, -1) != '/')
		{
			$path .= '/';
		}
		
		return $path;
	}
	
	/**
	 * Generate an application request URI segments array.
	 *
	 * Application request URI is different from Request URI in that it doesn't
	 * include the base path. So if your base path is '/base/path/' and
	 * your the request uri is '/base/path/item/id', the application request
	 * URI will be:
	 *
	 * <code>
	 * array('item', 'id')
	 * </code>
	 *
	 * This method checks if base path is in the REQUEST_URI. If it
	 * doesn't, it throws a RuntimeException. It then proceeds to
	 * cut query string and base path from REQUEST_URI, then exploding
	 * it to array of uri segments.
	 *
	 * @return array Application request URI in segments.
	 *
	 */
	protected function generateAppRequestURISegments()
	{
		if (strpos($this->server['REQUEST_URI'], $this->base_path) !== 0)
		{
			throw new \RuntimeException("Request object error! Base path is {$this->base_path}, but it doesn't exist in the REQUEST_URI ({$this->server['REQUEST_URI']}).");
		}
		
		// Remove base path
		$app_uri_string = substr($this->server['REQUEST_URI'], strlen($this->base_path));
		$pos = strpos($app_uri_string, '?');
		
		// If query string exists, remove it
		if ($pos !== FALSE)
		{
			$app_uri_string = substr($app_uri_string, 0, $pos);
		}
		
		// Explode it to segments
		$app_uri_string_expld = explode('/', $app_uri_string);
		$app_request_uri_segments = array();
		
		foreach ($app_uri_string_expld as $segment)
		{
			if (!empty($segment))
			{
				$app_request_uri_segments[] = urldecode($segment);
			}
		}
		
		return $app_request_uri_segments;
	}
	
	// ---------------------------------------------------------------
	
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