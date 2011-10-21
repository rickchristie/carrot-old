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
 * Native PHP session implementation.
 *
 * Does not use a storage. This is a very simple class that
 * allows object oriented way to access native PHP sessions.
 * Since it implements {@see SessionInterface}, it should be safe
 * using them, even though it accesses the superglobal directly.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Session;

class SimpleSession implements SessionInterface
{
    /**
     * @var bool TRUE if the session is started, FALSE otherwise.
     */
    protected $started = FALSE;
    
    /**
     * Starts the session.
     *
     * As a default convention sessions should not be started at all
     * when you instantiate any of the implementation of this
     * interface. You can start the session after instantiating with
     * this method instead.
     *
     * @return bool TRUE if successful, FALSE otherwise.
     *
     */
    public function start()
    {   
        if ($this->started)
        {
            return TRUE;
        }
        
        $this->started = session_start();
        return $this->started;
    }
    
    /**
     * Closes the session, provides behavior similar to PHP's
     * session_write_close().
     * 
     * A closed session should be allowed to be modified until it is
     * started again using {@see call()}. As a general rule, this
     * method should make the session class behave as if
     * session_write_close() is called.
     *
     */
    public function close()
    {
        if ($this->started == FALSE)
        {
            return;
        }
        
        session_write_close();
        $this->started = FALSE;
    }
    
    /**
     * Completely destroy the session data.
     * 
     * Unsets the session variables and removes the session cookie
     * (if any).
     * 
     * @return bool TRUE if successful, FALSE otherwise.
     *
     */
    public function destroy()
    {   
        $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
        
        $result = session_destroy();
        $this->started = !$result;
        return $result;
    }
        
    /**
     * Get the session data for the given index, or return default
     * value if it doesn't exist or if it fails to get the session
     * variable.
     *
     * @param string $index The index of the session data to get.
     * @param mixed $default The default value to return if the index
     *        doesn't exist.
     * @return mixed The session data for the given index, or the 
     *         default value set. If $index is NULL, the entire
     *         session data is returned instead.
     *
     */
    public function get($index = NULL, $default = NULL)
    {
        if ($this->started)
        {
            if ($index == NULL)
            {
                return $_SESSION;
            }
            
            if (array_key_exists($index, $_SESSION))
            {
                return $_SESSION[$index];
            }
        }
        
        return $default;
    }
    
    /**
     * Set the session data for the given index to the given value.
     *
     * @param string $index The index to set.
     * @param mixed $value The value to be set.
     *
     */
    public function set($index, $value)
    {
        if ($this->started)
        {
            $_SESSION[$index] = $value;
        }
    }
    
    /**
     * Remove the session data with the given index.
     *
     * @param string $index The index of the session data to be
     *        removed. If NULL, the whole session data is removed.
     *
     */
    public function remove($index = NULL)
    {
        if ($this->started)
        {
            if ($index == NULL)
            {
                $_SESSION = array();
                return;
            }
            
            unset($_SESSION[$index]);
        }
    }
    
    /**
     * Check if the index exists or not in the session data,
     * basically an isset() wrapper on the session index.
     * 
     * Will return FALSE if session is not started or in a closed
     * condition.
     *
     * @return bool TRUE if exists, FALSE otherwise.
     *
     */
    public function isIndexSet($index)
    {
        if ($this->started)
        {
            return isset($_SESSION[$index]);
        }
        
        return FALSE;
    }
}