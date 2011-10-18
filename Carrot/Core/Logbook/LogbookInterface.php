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
 * Logbook interface.
 *
//---------------------------------------------------------------
 * This is a contract between a Logbook implementation and
 * Carrot's core classes. 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Logbook;

interface LogbookInterface
{
    // TODO: Finish Logbook
    
    /**
     * Write a generic log.
     *
    //---------------------------------------------------------------
     * Aside for recording the fully qualified class name and the log
     * string itself, this method must also note the time of the log.
     * 
     * @param string $class Fully qualified class name of the class
     *        that issued the log.
     * @param string $log The log message to be written.
     *
     */
    public function write($class, $log);
    
    /**
     * Gets all written logs.
     *
    //---------------------------------------------------------------
     * This method must return an array with the following structure:
     *
     * <code>
     * 
     * </code>
     *
     * @return array 
     *
     */
    public function getAll();
}