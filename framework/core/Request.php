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
	
	// ---------------------------------------------------------------
	
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
}