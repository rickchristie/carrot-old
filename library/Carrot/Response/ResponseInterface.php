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
 * Response interface.
 *
 * This interface defines the contract between response objects
 * and Carrot's core classes.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Response;

interface ResponseInterface
{
    /**
     * Send the response to the client.
     *
     */
    public function send();
}