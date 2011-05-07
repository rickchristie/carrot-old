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
 * MySQL Database
 * 
 * A wrapper to MySQLi, uses prepared statements exclusively.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Driver;

class MySQLDatabase
{
    /**
     * @var string comments
     */
    protected $host;
    
    /**
     * @var type comments
     */
    protected $user;
    
    /**
     * @var type comments
     */
    protected $password;
    
    /**
     * @var type comments
     */
    protected $db_name;
    
    /**
     * @var type comments
     */
    protected $port;
    
    /**
     * @var type comments
     */
    protected $socket;
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    public function __construct()
    {
        
    }
}