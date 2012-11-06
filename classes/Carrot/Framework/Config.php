<?php

namespace Carrot\Framework;

/**
 * Encapsulates configuration data of the framework and your own
 * application. Configuration data is saved as key to value
 * basis. Anything can be the value, from objects to arrays.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Config
{
    /**
     * @see get()
     * @var array $data
     */
    private $data = array();
    
    /**
     * Set a configuration key and value.
     * 
     * @param string $key
     * @param mixed $value
     *
     */
    public function set($key, $value)
    {
        
    }
    
    /**
     * Get the configuration value of the given key. Returns default
     * value given if key does not exist.
     * 
     * @param string $key
     * @param mixed $default Optional, defaults to NULL.
     * @return mixed
     *
     */
    public function get($key, $default = NULL)
    {
        
    }
    
    /**
     * Returns TRUE if the configuration key has been set.
     * 
     * @param string $key
     * @return bool
     *
     */
    public function hasKey($key)
    {
        
    }
    
    /**
     * Returns TRUE if all of the given configuration keys given has
     * been set. Returns the array of keys that hasn't been set
     * if not.
     * 
     * @param array $keys
     * @return TRUE|array
     *
     */
    public function hasKeys(array $keys)
    {
        
    }
}