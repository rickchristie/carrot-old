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
 * Session
 * 
 * This class serves as a wrapper for PHP superglobal $_SESSION. We need a wrapper
 * so that we can easily mock and/or fake a session when testing. This class doesn't
 * access the superglobal $_SESSION directly so it's best that it is cached and shared
 * between objects. If two classes have different instances of this class, session
 * variables written in one will not be reflected at the other.
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class Session
{
	/**
	 * @var array List of session variables.
	 */
	protected $session;
	
	/**
	 * Constructs a Session object.
	 *
	 * Also starts the session using session_start(). Be sure to only
	 * instantiate this class only once, otherwise the session will
	 * be started more than once.
	 *
	 */
	public function __construct()
	{
		session_start();
		$this->session = $_SESSION;
	}
	
	/**
	 * Gets a session variable.
	 *
	 * Important Notice: This method doesn't access the superglobal
	 * $_SESSION, it accesses the class' local session array property.
	 * This means if there are two instances of Session, changes set
	 * using one will not be reflected using another.
	 *
	 * @param string $index
	 * @return mixed If index is empty, returns the whole array, if not, returns the index.
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
	 * Checks if the variable exists or not using isset.
	 *
	 * @param string $index
	 *
	 */
	public function indexIsset($index)
	{
		return isset($this->session[$index]);
	}
	
	/**
	 * Writes/overwrites a session variable.
	 * 
	 * @param string $index Session array index.
	 * @param string $content Session variable value.
	 *
	 */
	public function set($index, $content)
	{
		$_SESSION[$index] = $content;
		$this->session[$index] = $content;
	}
	
	/**
	 * Clears all session variable or remove a particular variable.
	 *
	 * @param string $index Session array index to remove.
	 *
	 */
	public function remove($index = '')
	{
		if (empty($index))
		{
			$_SESSION = array();
			$this->session = array();
			return;
		}
		
		unset($_SESSION[$index]);
		unset($this->session[$index]);
	}
	
	/**
	 * Destroys the session, acts as a wrapper for session_destroy().
	 *
	 */
	public function destroy()
	{
		$this->session = array();
		unset($_SESSION);
		session_destroy();
	}
	
	/**
	 * Acts as a wrapper for session_write_close().
	 *
	 */
	public function close()
	{
		session_write_close();
	}
	
	public function __destruct()
	{
		$this->close();
	}
}