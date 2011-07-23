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
 * Exception Logger Interface
 *
 * When you set a custom exception handler in PHP, it no longers
 * logs the errors for you, so we have to log the errors manually.
 * Carrot comes with a default vanilla implementation of this
 * interface, but you can implement your own if you'd like. Don't
 * forget to inject your implementation to the
 * ExceptionHandlerManager via the DIC.
 *
 * For more information please see the docs at
 * {@see Carrot\Core\ExceptionHandlerManager} and
 * {@see Carrot\Core\ExceptionLogger}.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

use Exception;

interface ExceptionLoggerInterface
{
    /**
     * asfd
     *
     */
    public function log(Exception $exception);
}