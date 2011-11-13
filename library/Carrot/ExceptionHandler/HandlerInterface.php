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
 * Exception handler interface.
 *
 * This interface defines the contract between the exception
 * handler and Carrot's core classes. The exception handler is
 * responsible for handling uncaught exception by displaying
 * either an error page or a debugging page and logging the
 * uncaught exception.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\ExceptionHandler;

use Exception,
    Carrot\Logbook;

interface HandlerInterface
{   
    /**
     * Handles the uncaught exception.
     *
     * Since the execution stops after this handler is finished, you
     * must send a response directly, bypassing
     * \Carrot\Core\Application. You don't need to call
     * set_exception_handler(), it will be called by
     * \Carrot\Core\Application.
     * 
     * Remember to log the uncaught exception manually since setting
     * the exception handler in PHP would disable the default error
     * logging behavior.
     * 
     * @param Exception $exception The uncaught exception.
     *
     */
    public function handle(Exception $exception);
}