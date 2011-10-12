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
 * Exception Logger
 *
 * This is the default implementation used by Carrot in logging
 * the exceptions. It does nothing fancy than emulating default
 * PHP error loggin behavior. Will only log the errors if
 * log_errors setting is turned on.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Exception;
use Carrot\Core\Interfaces\ExceptionLoggerInterface;

class ExceptionLogger implements ExceptionLoggerInterface
{
    /**
     * Emulates the default logging behavior of PHP.
     *
     * Will only log the exception if log_errors is activated in PHP.
     * Does not do anything fancier than calling vanilla error_log()
     * function.
     *
     * @param Exception $exception The exception instance to be logged.
     * 
     */
    public function log(Exception $exception)
    {   
        if (ini_get('log_errors'))
        {
            error_log($exception->__toString());
        }
    }
}