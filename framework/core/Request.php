<?php

/**
 * The short description
 *
 * As many lines of extendend description as you want {@link element}
 * links to an element
 * {@link http://www.example.com Example hyperlink inline link} links to
 * a website. The inline
 * source tag displays function source code in the description:
 * {@source } 
 * 
 * {@link http://www.example.com Read more}
 *
 * @package			package_name
 * @subpackage		sub package name, groupings inside of a project
 * @author 		  	author name <author@email>
 * @copyright		name date
 * @deprecated	 	description
 * @param		 	type [$varname] description
 * @return		 	type description
 * @since		 	a version or a date
 * @todo			phpdoc.de compatibility
 * @var				type	a data type for a class variable
 * @version			version
 */

class Request
{
	protected $server;
	protected $get;
	protected $post;
	protected $files;
	protected $cookie;
	protected $request;
	protected $env;
	protected $config;
	protected $uri_segments = array();
	
	// ---------------------------------------------------------------
	
	public function __construct($server, $get, $post, $files, $cookie, $request, $env, $config)
	{
		// Initialize properties
		$this->server = $server;
		$this->get = $get;
		$this->post = $post;
		$this->files = $files;
		$this->cookie = $cookie;
		$this->request = $request;
		$this->env = $env;
		$this->config = $config;
		
		/*
		|---------------------------------------------------------------
		| GENERATE URI SEGMENTS
		|---------------------------------------------------------------
		*/
		
		$request_uri = $this->server('REQUEST_URI');
		$path = $this->config->item('path');
		
		// Remove query string from $request_uri
		$pos = strpos($request_uri, '?');
		
		if ($pos !== false)
		{
			$request_uri = substr($request_uri, 0, $pos);
		}
		
		// Split the URL into segments
		if ($path != '/')
		{
			$uri_segments = explode('/', str_ireplace($path, '', $request_uri));
		}
		
		// Fill the uri_segments property, ignore empty segments
		foreach($uri_segments as $segment)
		{
			if (!empty($segment))
			{
				// We use rawurldecode instead of urldecode since
				// urldecode will decode '+' sign to space ' '
				$this->uri_segments[] = rawurldecode($segment);
			}
		}
		
		// If after all this the uri_segments is empty, then grab
		// the default controller name and generate uri_segments from it.
		
		if (empty($this->uri_segments))
		{
			$this->uri_segments = array();
			$exploded = explode('/', $this->config->item('default_controller_name'));
			
			// Fill the uri_segments property, ignore empty segments
			foreach ($exploded as $segment)
			{
				if (!empty($segment))
				{
					$this->uri_segments[] = $segment;
				}
			}
		}
	}
	
	// ---------------------------------------------------------------
	
	public function server($item = '')
	{
		if (empty($item))
		{
			return $this->server;
		}
		
		return $this->server[$item];
	}
	
	public function get($item = '')
	{
		if (empty($item))
		{
			return $this->get;
		}
		
		return $this->get[$item];
	}
	
	public function post($item = '')
	{
		if (empty($item))
		{
			return $this->post;
		}
		
		return $this->post[$item];
	}
	
	public function files($item = '')
	{
		if (empty($item))
		{
			return $this->files;
		}
		
		return $this->files[$item];
	}
	
	public function cookie($item = '')
	{
		if (empty($item))
		{
			return $this->cookie;
		}
		
		return $this->cookie[$item];
	}
	
	public function request($item = '')
	{
		if (empty($item))
		{
			return $this->request;
		}
		
		return $this->request[$item];
	}
	
	public function env($item = '')
	{
		if (empty($item))
		{
			return $this->env;
		}
		
		return $this->env[$item];
	}
	
	public function uri_segments($index = 'string')
	{
		if (!is_integer($index))
		{
			return $this->uri_segments;
		}
		
		return $this->uri_segments[$index];
	}
}