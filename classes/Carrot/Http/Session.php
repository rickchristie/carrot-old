<?php

namespace Carrot\Http;

use InvalidArgumentException;

/**
 * Native PHP session implementation.
 *
 * Does not use a storage. This is a very simple class that
 * allows object oriented way to access native PHP sessions.
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Session
{
    /**
     * If TRUE, then session has already started.
     * @var bool $started
     */
    private $started = FALSE;
    
    /**
     * @see __construct()
     * @var string $sessionName
     */
    private $sessionName;
    
    /**
     * Constructor.
     * 
     * PLEASE NOTE: This class assumes that the session is not
     * started yet. DO NOT start the session outside of this class
     * if you wanted to use this class.
     * 
     * @param string $sessionName The name of the session variable,
     *        if not set then defaults to PHPSESSID.
     *
     */
    public function __construct($sessionName = NULL)
    {
        if ($sessionName == NULL)
        {
            $this->sessionName = session_name();
            return;
        }
        
        session_name($sessionName);
        $this->sessionName = $sessionName;
    }
    
    /**
     * Starts the session.
     * 
     * PHP will not modify the cookie expiration time if the session
     * has already been set, so to start a remember me session, you
     * only have to start the long lasting session once:
     * 
     * <pre>
     * // Set the session time to expire in a week.
     * $session->start(604800);
     * </pre>
     * 
     * When you start the session without time parameter this method
     * will not overwrite the session cookie expiration time, so
     * after you start a long lasting session, every page could just
     * start the session using this:
     * 
     * <pre>
     * $session->start();
     * </pre>
     * 
     * However, every time you start a session with an expiration
     * time this method will *overwrite* the cookie expiration date
     * to the one you are setting.
     *
     * @param int $time Session expiration time (in seconds).
     *        Defaults to zero, which means the session will expire
     *        when the user closes the browser.
     * @return bool TRUE if successful, FALSE otherwise.
     *
     */
    public function start($time = 0)
    {   
        if ($this->started)
        {
            return TRUE;
        }
        
        if ($time < 0)
        {
            throw new InvalidArgumentException("Session expiration time must not be less than zero.");
        }
        
        if ($time == 0)
        {
            $this->started = session_start();
            return $this->started;
        }
        
        $this->startLongSession($time);
        return $this->started;
    }
    
    /**
     * If we no longer uses the session data, we can close the
     * session so that users can open tabs in parallel.
     *
     */
    public function writeClose()
    {
        session_write_close();
        $this->started = FALSE;
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
    
    /**
     * Starts a long session by using a long lasting session cookie.
     * 
     * @param int $time Time until the cookies expire, in seconds.
     *
     */
    private function startLongSession($time)
    {
        // If there is a cookie with the session name, PHP will
        // not change the expiration time of the cookie. To set
        // the expiration time we have to set the cookie ourselves
        // after we start the session.
        if (isset($_COOKIE[$this->sessionName]))
        {
            $this->started = session_start();
            $params = session_get_cookie_params();
            setcookie(
                $this->sessionName,
                session_id(),
                time() + $time,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        else
        {
            session_set_cookie_params($time, '/', '', FALSE, TRUE);
            $this->started = session_start();
        }
    }
}