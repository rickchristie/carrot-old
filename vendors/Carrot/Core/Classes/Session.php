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
 * Carrot's default Session class. This class serves as a wrapper for the superglobal
 * $_SESSION. It doesn't use any fancy storage mechanism, just plain old PHP session.
 * Starts session when constructed, that and the fact that it doesn't sync itself with
 * $_SESSION means that it must have a singleton lifecycle.
 *
 * Assumes that session IS NOT STARTED at construction
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class Session
{	
	/**
	 * @var bool TRUE if session has already been started, FALSE otherwise.
	 */
	protected $started;
	
	/**
	 * @var bool TRUE if session has been closed using session_write_close().
	 */
	protected $closed;
	
	/**
	 * Constructs a Session object.
	 *
	 * Also starts the session using session_start(). Be sure to only instantiate
	 * this class only once, otherwise PHP will raise a notice.
	 *
	 */
	public function __construct($session_is_started = FALSE, $session_write_closed = TRUE)
	{
		$this->started = $session_is_started;
		$this->closed = $session_write_closed;
	}
	
	public function start()
	{
		if (!$this->started)
		{
			session_start();
			$this->started = TRUE;
			$this->closed = FALSE;
		}
	}
	
	/**
	 * Destroys the session, acts as a wrapper for session_destroy().
	 *
	 * 
	 *
	 */
	public function destroy()
	{
		// Clears the data and the user's cookie
		$_SESSION = array();
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		session_destroy();
		
		// Set the session state
		$this->started = FALSE;
		$this->closed = TRUE;
	}
	
	/**
	 * Gets a session variable.
	 * 
	 * >> WARNING <<
	 *
	 * This method doesn't access the superglobal $_SESSION, it accesses
	 * the class' local session array property. This means if there are
	 * two instances of Session, changes set using one will not be reflected
	 * using another.
	 *
	 * @param string $index
	 * @return mixed If index is empty, returns the whole array, if not, returns the index.
	 *
	 */
	public function get($index = '')
	{
		if ($this->started && !$this->closed)
		{
			if (empty($index))
			{
				return $_SESSION;
			}
			
			return $_SESSION[$index];
		}
	}
	
	/**
	 * Checks if the variable exists or not using isset.
	 * 
	 * @param string $index
	 *
	 */
	public function indexIsset($index)
	{
		if ($this->started && !$this->closed)
		{
			return isset($_SESSION[$index]);
		}
	}
	
	/**
	 * Writes/overwrites a session variable.
	 *
	 * It writes to $_SESSION directly, but it also writes to the local
	 * Session:session property.
	 * 
	 * @param string $index Session array index.
	 * @param string $content Session variable value.
	 *
	 */
	public function write($index, $content)
	{
		if ($this->started && !$this->closed)
		{
			$_SESSION[$index] = $content;
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Clears all session variable or remove a particular variable.
	 *
	 * @param string $index Session array index to remove, if empty it will remove all session variables.
	 *
	 */
	public function unset($index = '')
	{
		if ($this->started && !$this->closed)
		{
			if (empty($index))
			{
				$_SESSION = array();
				return TRUE;
			}
			
			unset($_SESSION[$index]);
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function cacheExpire($new_cache_expire = '')
	{
		if (!empty($new_cache_expire))
		{
			session_cache_expire($new_cache_expire);
		}
		
		return session_cache_expire();
	}
	
	public function cacheLimiter($new_cache_limiter = '')
	{
		
	}
	
	public function decode()
	{
		
	}
	
	public function encode()
	{
		
	}
	
	public function getCookieParams()
	{
		
	}
	
	public function setCookieParams()
	{
		
	}
	
	public function id()
	{
		
	}
	
	public function moduleName()
	{
		
	}
	
	public function name()
	{
		
	}
	
	public function regenerateID()
	{
		
	}
	
	public function savePath()
	{
		
	}
	
	public function setSaveHandler()
	{
		
	}
	
	/**
	 * Acts as a wrapper for session_write_close().
	 *
	 */
	public function writeClose()
	{
		session_write_close();
		$this->started = FALSE;
		$this->closed = TRUE;
	}
	
	public function isStarted()
	{
		return $this->started;
	}
	
	public function __destruct()
	{
		$this->writeClose();
	}
}