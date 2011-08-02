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
 * Exception Handler Interface.
 *
// ---------------------------------------------------------------
 * This interface defines the contract between 
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
    // ---------------------------------------------------------------
     * 
     * 
     * @param Exception $exception The uncaught exception to handle.
     * @return \Carrot\Core\Response The response to be sent to the browser.
     *
     */
    public function handle(Exception $exception);
}