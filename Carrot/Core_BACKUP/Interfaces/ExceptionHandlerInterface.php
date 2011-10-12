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
 * Exception Handler Interface.
 *
 * This interface defines the contract between your exception
 * handler class with Carrot\Core\ExceptionHandlerManager class.
 * All exception handlers must implement this interface, or the
 * exception handler manager will choke.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

use Exception;

interface ExceptionHandlerInterface
{
    /**
     * Handles the uncaught exception.
     * 
     * Use this method to handle the exception, after that, return an
     * instance of \Carrot\Core\Response to be returned to the user,
     * preferably containing the HTML markup for either a debugging
     * page or an error page for the user.
     * 
     * @param Exception $exception The uncaught exception to handle.
     * @return \Carrot\Core\Response The response to be sent to the browser.
     *
     */
    public function handle(Exception $exception);
}