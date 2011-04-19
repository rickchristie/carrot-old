<?php

/**
 * URL
 *
 * Copyright (c) 2011 Ricky Christie
 *
 * Licensed under the MIT License.
 *
 * Helper class, used to 
 *
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 */

class URL
{
	/**
	 * @var string Protocol used to construct URL, it's either 'http' or 'https'.
	 */
	protected $protocol;
	
	/**
	 * @var string Domain name 
	 */
	protected $domain;
	
	/**
	 * @var string Path from the root of the domain name to the directory of the index.php file.
	 */
	protected $path;
	
	/**
	 * Defies imagination, extends boundaries and saves the world ...all before breakfast!
	 *
	 */
	public function __construct(array $server, $protocol = '', $domain = '', $path = '')
	{
		if (!in_array($protocol, array('http', 'https')))
		{
			$protocol = strtolower(substr($server['SERVER_PROTOCOL'], 0, 5)) == 'https' ? 'https' : 'http';
		}
		
		if (empty($domain))
		{
			$domain = $server['SERVER_NAME'];
		}
		
		if (empty($path))
		{
			$path = str_ireplace('/index.php', '', $server['SCRIPT_NAME']);
		}
		
		// Add trailing slash to default path, if it doesn't have it
		if (empty($path) or substr($path, -1) != '/')
		{
			$path .= '/';
		}
		
		$this->protocol = $protocol;
		$this->domain = $domain;
		$this->path = $path;
	}
	
	public function base()
	{
		return $this->protocol . '://' . $this->domain . $this->path;
	}
	
	public function parse($segments)
	{
		if (is_string($segments))
		{
			$segments = explode('/', $segments);
		}
		
		$segment_str = '';
		
		foreach ($segments as $segment)
		{
			if (!empty($segment))
			{
				$segment_str .= rawurlencode($segment) . '/';
			}
		}
		
		return $this->base() . $segment_str;
	}
	
	// ---------------------------------------------------------------
	
	protected function default_protocol()
	{
		
	}
	
	protected function default_domain()
	{
		
	}
	
	protected function default_path()
	{
		
	}
}