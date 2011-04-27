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
 * Error Handler Interface
 * 
 * This interface defines the contract between the Error Handler class and
 * the framework. Should you feel the need to replace the DefaultErorHandler
 * with your own class, you should implement this interface. The front controller
 * will use these methods.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

interface ErrorHandlerInterface
{
    /**
     * Set error and exception handler, preferably to a method in this class.
     *
     */
    public function set();
    
    /**
     * Restore error and exception handler previously set by ErrorHandlerInterface::set().
     *
     */
    public function restore();
}