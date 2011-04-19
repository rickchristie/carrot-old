<?php

/**
 * Session
 *
 * Copyright (c) 2011 Ricky Christie
 *
 * Licensed under the MIT License.
 *
 * This class serves as a wrapper for PHP superglobal $_SESSION. We need a wrapper
 * so that we can easily mock and/or fake a session when testing. This class doesn't
 * access the superglobal $_SESSION directly so it's best that it is cached and shared
 * between objects. If two classes have different instances of this class, session
 * variables written in one will not be reflected at the other.
 *
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 */

class Session
{
	/**
	 * @var array List of session variables.
	 */
	protected $session;
	
	/**
	 * Constructs a Session object.
	 *
	 * @param array $session Current session variables.
	 *
	 */
	public function __construct(array $session)
	{
		$this->session = $session;
	}
	
	/**
	 * Gets a session variable.
	 *
	 * Important Notice: This method doesn't access the superglobal
	 * $_SESSION, it accesses the class' local session array property.
	 * This means if there are two instances of Session, changes set
	 * using one will not be reflected using another.
	 *
	 */
	public function get($index = '')
	{
		if (empty($index))
		{
			return $this->session;
		}
		
		return $this->session[$index];
	}
	
	/**
	 * Writes/overwrites a session variable.
	 * 
	 * 
	 *
	 */
	public function set($index, $content)
	{
		$_SESSION[$index] = $content;
		$this->session[$index] = $content;
	}
	
	public function remove($index = '')
	{
		
	}
	
	public function destroy()
	{
		
	}
	
	public function close()
	{
		
	}
}