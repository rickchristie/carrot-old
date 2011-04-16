<?php

/**
 * Request object
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
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 */

/**
 * Request Object.
 *
 * Represents the actual request to the server. Accepts and stores $_SERVER,
 * $_GET, $_POST, $_FILES, $_COOKIE, $_REQUEST, $_ENV.
 * 
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 * @todo		
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