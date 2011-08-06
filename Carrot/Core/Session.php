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
 * This is Carrot's default session wrapper class. It wraps
 * interaction between your classes and PHP's default session
 * handlers. For it to function correctly you need to assign
 * singleton lifecycle to the instance.
 *
 * Assumes that session is not yet started when instantiated
 * (unless specified otherwise when constructing).
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class Session
{
    /**
     * @var bool True if session_start() has been called at least once, false otherwise.
     */
    protected $hasBeenStartedBefore;

    /**
     * @var bool True if session is currently started, false otherwise.
     */
    protected $started;
    
    /**
     * @var bool True if session is currently closed using session_write_close(), false otherwise.
     */
    protected $closed;
    
    /**
     * Constructor.
     * 
     * Assumes that it is instantiated at early bootstraping, where
     * session is not started yet. This is the best possible scenario
     * to use this class.
     * 
     * @param bool $sessionIsStarted True if the session is currently started, defaults to false.
     * @param bool $sessionWriteClosed True if the session is currently write closed, defaults to false.
     * @param bool $sessionHasBeenStartedBefore True if session has been started at least once, defaults to false.
     *
     */
    public function __construct($sessionIsStarted = false, $sessionWriteClosed = true, $sessionHasBeenStartedBefore = false)
    {
        $this->started = $sessionIsStarted;
        $this->closed = $sessionWriteClosed;
        
        if ($this->started)
        {
            $this->hasBeenStartedBefore = true;
        }
        else
        {
            $this->hasBeenStartedBefore = $sessionHasBeenStartedBefore;
        }
    }
    
    /**
     * Starts the session.
     *
     * Assuming that the booleans that were given at the constructor
     * is true and your application always uses this instance
     * (singleton lifecycle) to access the $_SESSION superglobal and
     * its functions, you can safely call this function numerous
     * times, just to make sure the session is writable.
     *
     */
    public function start()
    {
        if (!$this->started)
        {
            session_start();
            $this->started = true;
            $this->closed = false;
            $this->hasBeenStartedBefore = true;
        }
    }
    
    /**
     * Destroys the session, acts as a wrapper for session_destroy().
     * 
     * Destroys both the data and the cookie for the session ID at the
     * client side. Will not run if $started property is false or if
     * $closed property is true.
     *
     * @return bool True if destroyed, false if session is not started or is currently write closed.
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
            $this->started = false;
            $this->closed = true;
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Gets a session variable.
     * 
     * This method will not return anything if the session is not
     * started or is currently write closed, so make sure you call
     * start() before using this method.
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
     * When checking value with this method, make sure you do a strict
     * compare (===). This method returns null if the session is not
     * started or it is currently write closed. You can call start()
     * before usage to make sure that session is started and writable.
     * 
     * @param string $index Session array index to be checked using isset().
     *
     */
    public function varExists($index)
    {
        if ($this->started && !$this->closed)
        {
            return isset($_SESSION[$index]);
        }
    }
    
    /**
     * Writes/overwrites a session variable.
     * 
     * This method will return false if the session is not started or
     * if the session is currently write closed. Use start() to make
     * sure that the variable is writable before using this method.
     * 
     * @param string $index Session array index to be written on.
     * @param string $content Value used to write.
     * @return bool True if the write is sucessful, false otherwise.
     *
     */
    public function write($index, $content)
    {
        if ($this->started && !$this->closed)
        {
            $_SESSION[$index] = $content;
            return true;
        }
        
        return false;
    }
    
    /**
     * Clears all session variable or remove a particular variable.
     *
     * Will return false if session is not started or if the session
     * is currently write closed. Use start() to make sure that
     * variable is writable.
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
                return true;
            }
            
            unset($_SESSION[$index]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Acts as a wrapper for session_write_close().
     *
     * Will also update the state variables in this Session class.
     * Will not try to close the session if it is not currently
     * started or if it is currently closed.
     *
     */
    public function writeClose()
    {
        if ($this->started && !$this->closed)
        {
            session_write_close();
            $this->started = false;
            $this->closed = true;
        }
    }
        
    /**
     * Wrapper for session_name().
     *
     * This method will not allow you to change the session name if
     * session has already been started before. This is useful since
     * changing session name after a session has been started will
     * change the value but doesn't change the actual header sent.
     *
     * @param string $newName Optional. If not specified will return current session name.
     *
     */
    public function name($newName = '')
    {
        if (!empty($newName) && !$this->hasBeenStartedBefore)
        {
            return session_name($newName);
        }
        
        return session_name();
    }
    
    /**
     * Wrapper for session_id().
     *
     * @param string $newID Optional. If not specified will return current session id.
     *
     */
    public function id($newID = '')
    {
        if (!empty($newID))
        {
            return session_id($newID);
        }
        
        return session_id();
    }
    
    /**
     * Wrapper for session_regenerate_id().
     *
     * @param bool $deleteOldSession Whether to delete the old associated session file or not.
     * @return bool True on success, false on failure.
     *
     */
    public function regenerateID($deleteOldSession = false)
    {
        return session_regenerate_id($deleteOldSession);
    }
    
    /**
     * Returns true if session is currently started, false otherwise.
     *
     * @return bool 
     *
     */
    public function isStarted()
    {
        return $this->started;
    }
    
    /**
     * Returns true if session is currently write closed, false otherwise.
     *
     * @return bool
     *
     */
    public function isWriteClosed()
    {
        return $this->closed;
    }
    
    /**
     * Returns true if session has already been started at least once, false otherwise.
     *
     * @return bool
     *
     */
    public function hasBeenStartedBefore()
    {
        return $this->hasBeenStartedBefore;
    }
    
    /**
     * Destroys the session, performs Session::writeClose().
     *
     */
    public function __destruct()
    {
        $this->writeClose();
    }
    
    /**
     * Wrapper for session_decode().
     *
     * @param string Encoded session data.
     * @return bool True on success, false on failure.
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
     * This method will not allow you to change the session cache
     * expire if session has already been started before. This is
     * useful since changing cache expire after session has already
     * started will change the saved value for cache expire but
     * doesn't change anything in the header at all.
     *
     * @param string $newCacheExpire New cache expire time (in minutes).
     * @return string Current session cache expire.
     *
     */
    public function cacheExpire($newCacheExpire = '')
    {
        if (!empty($newCacheExpire) && !$this->hasBeenStartedBefore)
        {
            return session_cache_expire($newCacheExpire);
        }
        
        return session_cache_expire();
    }
    
    /**
     * Wrapper for session_cache_limiter().
     *
     * This method will not allow you to change the session cache
     * limiter if session has already been started before. This is
     * useful since change cache limiter after after session has
     * already started will change the saved value but doesn't change
     * anything in the header at all.
     *
     * @param string $new_cache_limiter New cache limiter (public, private_no_expire, private, or nocache).
     * @return string Name of the current cache limiter.
     *
     */
    public function cacheLimiter($new_cache_limiter = '')
    {
        if (!empty($new_cache_limiter) && !$this->hasBeenStartedBefore)
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
     * been started before. Unlike the session function, none of the
     * arguments are optional.
     * 
     * @param int $lifetime Lifetime of the session cookie, defined in seconds.
     * @param string $path Path on the domain where the cookie will work. Use a single slash ('/') for all paths on the domain.
     * @param string $domain Cookie domain, for example 'www.php.net'. To make cookies visible on all subdomains then the domain must be prefixed with a dot like '.php.net'.
     * @param bool $secure If true cookie will only be sent over secure connections.
     * @param bool $httponly If set to true then PHP will attempt to send the httponly flag when setting the session cookie.
     * @return bool True if it changes the cookie parameters, false if it doesn't.
     *
     */
    public function setCookieParams($lifetime, $path, $domain, $secure, $httponly)
    {
        if (!$this->hasBeenStartedBefore)
        {
            session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
            return true;
        }
        
        return false;
    }
    
    /**
     * Wrapper for session_module_name(). 
     *
     * @param string $module If module is specified, that module will be used instead.
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
     * Does not let you change session save path if session has been
     * started already. This is useful since changing session save
     * path after session has been started can potentially lead to
     * unpredictable behavior.
     * 
     * @param string $newPath Session data path. If specified, the path to which data is saved will be changed.
     * @return string Path of the current directory used for data storage.
     *
     */ 
    public function savePath($newPath = '')
    {   
        if (!empty($newPath) && !$this->hasBeenStartedBefore)
        {
            return session_save_path($newPath);
        }
        
        return session_save_path();
    }
    
    /**
     * Wrapper for session_set_save_handler().
     * 
     * Does not let you change the session save handler if session has
     * been started before, for obvious reasons.
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
        if (!$this->hasBeenStartedBefore)
        {
            return session_set_save_handler($open, $close, $read, $write, $destroy, $gc);
        }
        
        return false;
    }
}