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
 * Exception Page Interface
 *
 * In the event that an exception occurs, execution of the script
 * stops and Carrot's ExceptionHandler will load a page. This
 * interface represents the contract between the exception page
 * template object with the ExceptionHandler. You can implement
 * this class to create your production-server error page.
 *
 * Modify the provider class for ExceptionHandler to inject your
 * implementation of this interface.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

use Exception;

interface ExceptionPageInterface
{
    /**
     * Displays the exception page.
     * 
     * Since an exception has already occured, we are no longer
     * required to return an implementation of ResponseInterface. In
     * this method you are to echo the contents of the exception page
     * template directly to the client.
     * 
     */
    public function display();
    
    /**
     * Sets the exception object to be displayed.
     *
     * This method will be called BEFORE display() is called.
     *
     * @param Exception $exception
     *
     */
    public function setException(Exception $exception);
}