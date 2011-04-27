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
 * Carrot's default Session wrapper class. It wraps interaction between your classes
 * with PHP's default session handlers. For it to function correctly:
 *
 *   1) You need to assign singleton lifecycle to the instance.
 *   2) Every PHP session related function should be called using the wrapper.
 *
 * Assumes that session is not yet started when instantiated.
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class Session
{
	/**
	 * @var bool TRUE if session_start() has been called at least once, FALSE otherwise.
	 */
	protected $has_been_started_before;

	/**
	 * @var bool TRUE if session is currently started, FALSE otherwise.
	 */
	protected $started;
	
	/**
	 * @var bool TRUE if session is currently closed using session_write_close(), FALSE otherwise.
	 */
	protected $closed;
	
	/**
	 * Constructs a Session object.
	 * 
	 * Assumes that it is instantiated at early bootstraping, where session is not started yet.
	 * This is the best possible scenario to use this class.
	 * 
	 * @param bool $session_is_started Optional. TRUE if the session is currently started, defaults to FALSE.
	 * @param bool $session_write_closed Optional. TRUE if the session is currently write closed, defaults to FALSE.
	 * @param bool $session_has_been_started_before Optional. TRUE if session has been started at least once, defaults to FALSE.
	 *
	 */
	public function __construct($session_is_started = FALSE, $session_write_closed = TRUE, $session_has_been_started_before = FALSE)
	{
		$this->started = $session_is_started;
		$this->closed = $session_write_closed;
		
		if ($this->started)
		{
			$this->has_been_started_before = TRUE;
		}
		else
		{
			$this->has_been_started_before = $session_has_been_started_before;
		}
	}
	
	/**
	 * Starts the session.
	 *
	 * Assuming that the booleans that were given at the constructor is true and your
	 * application always uses this class (singleton lifecycle) to access the $_SESSION
	 * superglobal and its functions, you can safely call this function numerous times,
	 * just to make sure the session is writable.
	 *
	 */
	public function start()
	{
		if (!$this->started)
		{
			session_start();
			$this->started = TRUE;
			$this->closed = FALSE;
			$this->has_been_started_before = TRUE;
		}
	}
	
	/**
	 * Destroys the session, acts as a wrapper for session_destroy().
	 * 
	 * Destroys both the data and the cookie for the session ID at the
	 * client side. Will not run if Session::started is FALSE or if
	 * Session::closed is TRUE.
	 *
	 * @return bool TRUE if destroyed, FALSE if session is not started or is currently write closed.
	 * 
	 */
	public function destroy()
	{
		if ($this->started && !$this->closed)
		{
			// Clears the data and the user's cookie
			$_SESSION = array();
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
			session_destroy();
			
			// Set the session state
			$this->started = FALSE;
			$this->closed = TRUE;
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Gets a session variable.
	 * 
	 * This method will not return anything if the session is not started
	 * or is currently write closed, so make sure you call Session::start()
	 * before using this method.
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
	 * When checking value with this method, make sure you do a strict compare (===).
	 * This method returns NULL if the session is not started or it is currently
	 * write closed. You can call Session::start() before usage to make sure that
	 * session is started and writable.
	 * 
	 * @param string $index Session array index to be checked using isset().
	 *
	 */
	public function varIsset($index)
	{
		if ($this->started && !$this->closed)
		{
			return isset($_SESSION[$index]);
		}
	}
	
	/**
	 * Writes/overwrites a session variable.
	 * 
	 * This method will return FALSE if the session is not started or if the session
	 * is currently write closed. Use Session::start() to make sure that the variable
	 * is writable before using this method.
	 * 
	 * @param string $index Session array index to be written on.
	 * @param string $content Value used to write.
	 * @return bool TRUE if the write is sucessful, FALSE otherwise.
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
	 * Will return FALSE if session is not started or if the session is currently write
	 * closed. Use Session::start() to make sure that variable is writable.
	 * 
	 * @param string $index Session array index to remove, if empty it will remove all session variables.
	 *
	 */
	public function remove($index = '')
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
	
	/**
	 * Acts as a wrapper for session_write_close().
	 *
	 * Will also update the state variables in this Session class. Will not try
	 * to close the session if it is not currently started or if it is currently
	 * closed.
	 *
	 */
	public function writeClose()
	{
		if ($this->started && !$this->closed)
		{
			session_write_close();
			$this->started = FALSE;
			$this->closed = TRUE;
		}
	}
		
	/**
	 * Wrapper for session_name().
	 *
	 * This method will not allow you to change the session name
	 * if session has already been started before. This is useful since
	 * changin session name after a session has been started will change
	 * the value but doesn't change the actual header sent.
	 *
	 * @param string $new_name Optional. If not specified will return current session name.
	 *
	 */
	public function name($new_name = '')
	{
		if (!empty($new_name) && !$this->has_been_started_before)
		{
			return session_name($new_name);
		}
		
		return session_name();
	}
	
	/**
	 * Wrapper for session_id().
	 *
	 * @param string $new_id Optional. If not specified will return current session id.
	 *
	 */
	public function id($new_id = '')
	{
		if (!empty($new_id))
		{
			return session_id($new_id);
		}
		
		return session_id();
	}
	
	/**
	 * Wrapper for session_regenerate_id().
	 *
	 * @param bool $delete_old_session Whether to delete the old associated session file or not.
	 * @return bool TRUE on success, FALSE on failure.
	 *
	 */
	public function regenerateID($delete_old_session = FALSE)
	{
		return session_regenerate_id($delete_old_session);
	}
	
	/**
	 * Returns TRUE if session is currently started, FALSE otherwise.
	 *
	 * @return bool 
	 *
	 */
	public function isStarted()
	{
		return $this->started;
	}
	
	/**
	 * Returns TRUE if session is currently write closed, FALSE otherwise.
	 *
	 * @return bool
	 *
	 */
	public function isWriteClosed()
	{
		return $this->closed;
	}
	
	/**
	 * Returns TRUE if session has already been started at least once, FALSE otherwise.
	 *
	 * @return bool
	 *
	 */
	public function hasBeenStartedBefore()
	{
		return $this->has_been_started_before;
	}
	
	/**
	 * Destroys the session, performs Session::writeClose().
	 *
	 */
	public function __destruct()
	{
		$this->writeClose();
	}
	
	// ---------------------------------------------------------------
	
	/**
	 * Wrapper for session_decode().
	 *
	 * @param string Encoded session data.
	 * @return bool TRUE on success, FALSE on failure.
	 *
	 */
	public function decode($string)
	{
		return session_decode($string);
	}
	
	/**
	 * Wrapper for session_encode().
	 *
	 * @return string Encoded session data.
	 *
	 */
	public function encode()
	{
		return session_encode();
	}
	
	/**
	 * Wrapper for session_cache_expire().
	 *
	 * This method will not allow you to change the session cache expire
	 * if session has already been started before. This is useful since
	 * changing cache expire after session has already started will change
	 * the saved value for cache expire but doesn't change anything in the
	 * header at all.
	 *
	 * @param string $new_cache_expire New cache expire time (in minutes).
	 * @return string Current session cache expire.
	 *
	 */
	public function cacheExpire($new_cache_expire = '')
	{
		if (!empty($new_cache_expire) && !$this->has_been_started_before)
		{
			return session_cache_expire($new_cache_expire);
		}
		
		return session_cache_expire();
	}
	
	/**
	 * Wrapper for session_cache_limiter().
	 *
	 * This method will not allow you to change the session cache limiter
	 * if session has already been started before. This is useful since
	 * change cache limiter after after session has already started will
	 * change the saved value but doesn't change anything in the header
	 * at all.
	 *
	 * @param string $new_cache_limiter New cache limiter (public, private_no_expire, private, or nocache).
	 * @return string Name of the current cache limiter.
	 *
	 */
	public function cacheLimiter($new_cache_limiter = '')
	{
		if (!empty($new_cache_limiter) && !$this->has_been_started_before)
		{
			return session_cache_limiter($new_cache_limiter);
		}
		
		return session_cache_limiter();
	}
	
	/**
	 * Wrapper for session_get_cookie_params().
	 *
	 * @return array Cookie parameters ('lifetime', 'path', 'domain', 'secure', 'httponly').
	 *
	 */
	public function getCookieParams()
	{
		return session_get_cookie_params();
	}
	
	/**
	 * Wrapper for session_set_cookie_params().
	 *
	 * Does not let you set cookie parameters when session has already
	 * been started before. Unlike the session function, none of the arguments
	 * are optional.
	 * 
	 * @param int $lifetime Lifetime of the session cookie, defined in seconds.
	 * @param string $path Path on the domain where the cookie will work. Use a single slash ('/') for all paths on the domain.
	 * @param string $domain Cookie domain, for example 'www.php.net'. To make cookies visible on all subdomains then the domain must be prefixed with a dot like '.php.net'.
	 * @param bool $secure If TRUE cookie will only be sent over secure connections.
	 * @param bool $httponly If set to TRUE then PHP will attempt to send the httponly flag when setting the session cookie.
	 * @return bool TRUE if it changes the cookie parameters, FALSE if it doesn't.
	 *
	 */
	public function setCookieParams($lifetime, $path, $domain, $secure, $httponly)
	{
		if (!$this->has_been_started_before)
		{
			session_set_cookie_params($lifetimae, $path, $domain, $secure, $httponly);
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Wrapper for session_module_name(). 
	 *
	 * @param string $modul If module is specified, that module will be used instead.
	 * @return string Returns the name of the current session module.
	 *
	 */
	public function moduleName($module = '')
	{
		if (!empty($module))
		{
			return session_module_name($module);
		}
		
		return session_module_name();
	}
	
	/**
	 * Wrapper for session_save_path().
	 *
	 * Does not let you change session save path if session has been started already.
	 * This is useful since changing session save path after session has been started
	 * can potentially lead to unpredictable behavior.
	 * 
	 * @param string $new_path Session data path. If specified, the path to which data is saved will be changed.
	 * @return string Path of the current directory used for data storage.
	 *
	 */	
	public function savePath($new_path = '')
	{	
		if (!empty($new_path) && !$this->has_been_started_before)
		{
			return session_save_path($new_path);
		}
		
		return session_save_path();
	}
	
	/**
	 * Wrapper for session_set_save_handler().
	 * 
	 * Does not let you change the session save handler if session has been started
	 * before, for obvious reasons.
	 *
	 * @param callback $open Open function, this works like a constructor in classes and is executed when the session is being opened.
	 * @param callback $close Close function, this works like a destructor in classes and is executed when the session operation is done.
	 * @param callback $read Read function must return string value always to make save handler work as expected.
	 * @param callback $write Write function that is called when session data is to be saved. This function expects two parameters: an identifier and the data associated with it.
	 * @param callback $destroy The destroy handler, this is executed when a session is destroyed with session_destroy() and takes the session id as its only parameter.
	 * @param callback $gc The garbage collector, this is executed when the session garbage collector is executed and takes the max session lifetime as its only parameter.
	 *
	 */
	public function setSaveHandler($open, $close, $read, $write, $destroy, $gc)
	{
		if (!$this->has_been_started_before)
		{
			return session_set_save_handler($open, $close, $read, $write, $destroy, $gc);
		}
		
		return FALSE;
	}
}