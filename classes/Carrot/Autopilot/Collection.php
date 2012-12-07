<?php

namespace Carrot\Autopilot;

/**
 * Simple key value based cache. Used to store instantiated
 * objects, instantiated contexts and identifiers.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Collection
{
    /**
     * @see get()
     * @var array $data
     */
    private $data = array();
    
    /**
     * Set key and the value.
     * 
     * @param string $key
     * @param mixed $value
     *
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
    
    /**
     * Returns TRUE if the given key has been set in this collection.
     * 
     * @param string $key
     *
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }
    
    /**
     * Get the cached value. Returns the default value if it doesn't
     * exist.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     *
     */
    public function get($key, $default = NULL)
    {
        if (array_key_exists($key, $this->data) == FALSE)
        {
            return $default;
        }
        
        return $this->data[$key];
    }
}