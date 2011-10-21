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
 * Session interface.
 *
 * This interface is useful if you want some indirection. All of
 * the session classes in this package implements this interface.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Session;

interface SessionInterface
{
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
    public function start();
    
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
    public function close();
    
    /**
     * Completely destroy the session data.
     * 
     * Unsets the session variables and removes the session cookie
     * (if any).
     * 
     * @return bool TRUE if successful, FALSE otherwise.
     *
     */
    public function destroy();
        
    /**
     * Get the session data for the given index, or return default
     * value if it doesn't exist.
     *
     * @param string $index The index of the session data to get.
     * @param mixed $default The default value to return if the index
     *        doesn't exist.
     * @return mixed The session data for the given index, or the 
     *         default value set. If $index is NULL, the entire
     *         session data is returned instead.
     *
     */
    public function get($index = NULL, $default = NULL);
    
    /**
     * Set the session data for the given index to the given value.
     *
     * @param string $index The index to set.
     * @param mixed $value The value to be set.
     *
     */
    public function set($index, $value);
    
    /**
     * Remove the session data with the given index.
     *
     * @param string $index The index of the session data to be
     *        removed. If NULL, the whole session data is removed.
     *
     */
    public function remove($index = NULL);
    
    /**
     * Check if the index exists or not in the session data,
     * basically an isset() wrapper on the session index.
     *
     * @return bool TRUE if exists, FALSE otherwise.
     *
     */
    public function isIndexSet($index);
}