<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Carrot's default logbook implementation.
 *
 * This class is responsible for storing the log data of
 * dependency injection container, autoloader, event dispatcher
 * and router for debugging purposes. This class allows exception
 * handlers to access Carrot's core classes' log without
 * accessing each individual classes.
 *
 * As long as core classes use this object appropriately, this
 * class should contain chronological and categorical information
 * which is more readable than regular stack trace.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Logbook;

class Logbook implements LogbookInterface
{
    // TODO: Finish Logbook
    
    /**
    //---------------------------------------------------------------
     * @var array Contains logs
     */
    protected $logs = array();
    
    public function write($class, $message)
    {
        
    }
    
    public function getAll()
    {
        
    }
}