<?php

namespace Carrot\Framework\Error;

use Exception;

/**
 * The interface that you must extend if you wanted to hook your
 * exception handler to Carrot.
 * 
 * Carrot will provide extra information to your exception
 * handler, allowing you to send better information if you need
 * it. It will instantiate your exception handler class with
 * the dependency injection container.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

interface ExceptionHandlerInterface
{
    /**
     * Handles the uncaught exception.
     * 
     * PHP will stop executing after the exception is handled, so
     * make sure you echo out a proper '500 internal server error'
     * page to the viewer.
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