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
 * CLI response.
 *
 * This is a value object that represents a generic CLI response.
 * It prints the response body and exit with the given status.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Response;

class CLIResponse implements ResponseInterface
{
    /**
     * @var string The body of the response, to be printed out to the
     *      console.
     */
    protected $body;
    
    /**
     * @var int The exit status number.
     */
    protected $exitStatus;
    
    /**
     * Constructor.
     * 
     * @param string $body The body of the response, to be printed
     *        out to the console.
     * @param int $exitStatus The exit status number.
     * 
     */
    public function __construct($body, $exitStatus = 0)
    {
        $this->body = $body;
        $this->exitStatus = (int) $exitStatus;
    }
    
    /**
     * Send the response to the client.
     * 
     * Exits with the status code given at object construction.
     *
     */
    public function send()
    {
        echo $this->body;
        exit($this->exitStatus);
    }
}