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
 * Response Interface
 * 
 * This interface represents the contract between the Response class with
 * the front controller. This class is important because it is the what the
 * front controller expects as a return value from each controller method it
 * calls. The response object represents the application's response to a
 * specific request.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

interface ResponseInterface
{
    /**
     * Sends (echoes out) the response to the client.
     *
     */
    public function send();
}