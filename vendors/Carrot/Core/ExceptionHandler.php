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
 * Exception Handler
 * 
 * Carrot's exception handler aims to display adequate information
 * when an exception occurs, you can modify the exception page
 * template by implementing ExceptionPageInterface on your own and
 * injecting your implementation at this class's provider class
 * code.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Carrot\Core\Interfaces\ExceptionPageInterface;
use Carrot\Core\DevelopmentExceptionPage;
use InvalidArgumentException;
use Exception;

class ExceptionHandler
{
    /**
     * @var bool True if exception handler is set, false otherwise.
     */
    protected $set = false;
    
    /**
     * @var ExceptionPageInterface The object responsible to displaying the exception page.
     */
    protected $exceptionPage;
    
    /**
     * Constructs the ExceptionHandler object.
     *
     * Carrot uses DevelopmentExceptionPage class as the default
     * exception page class. You can change the template being
     * displayed by implementing ExceptionPageInterface and injecting
     * your implementation at this class's provider.
     *
     * @throws InvalidArgumentException
     * @param string $serverProtocol The server protocol of this request, e.g. 'HTTP/1.0'.
     * @param ExceptionPageInterface $exceptionPage The exception page template to be displayed on production.
     *
     */
    public function __construct($serverProtocol, ExceptionPageInterface $exceptionPage = null)
    {
        $this->serverProtocol = $serverProtocol;
        
        if ($exceptionPage)
        {
            $this->exceptionPage = $exceptionPage;
        }
        else
        {
            $this->exceptionPage = new DevelopmentExceptionPage;
        }
    }
    
    /**
     * Carrot's exception handler.
     * 
     * Gets and clean the output buffer before handling the exception.
     * It tries to log the exception if log_errors is on. It also tries
     * to send 500 Internal Server Error header if header is not sent
     * already.
     * 
     * @param Exception $exception The exception that was thrown.
     * 
     */
    public function exceptionHandler(Exception $exception)
    {
        // We wrap everything in this exception handler in a try catch
        // block so that if an exception occurs when we are handling
        // an exception, we still get an error message instead of a
        // debugging nightmare.        
        try
        {        
            $outputBuffer = $this->getAndCleanOutputBuffer();
            
            // PHP doesn't log the errors when we have custom exception
            // handler set, so we have to call error_log manually.
            if (ini_get('log_errors'))
            {
                error_log($exception->__toString());
            }
            
            if (!headers_sent())
            {
                header($this->serverProtocol . ' 500 Internal Server Error');
            }
            
            $this->exceptionPage->setException($exception);
            $this->exceptionPage->display();
        }
        catch (Exception $exceptionWithinException)
        {
            echo get_class($exceptionWithinException) . ' thrown within the exception handler. Message: ' . $exceptionWithinException->getMessage() . ' on line ' . $exceptionWithinException->getLine();
        }
        
        exit;
    }
    
    /**
     * Sets the exception handler.
     *
     * Checks first so that we don't accidentally set the exception
     * handler twice.
     *
     */
    public function set()
    {
        if (!$this->set)
        {
            set_exception_handler(array($this, 'exceptionHandler'));
        }
    }
    
    /**
     * Restores the exception handler.
     *
     * Checks first so that we don't accidentally restore the
     * exception handler even though we haven't set it.
     *
     */
    public function restore()
    {
        if ($this->set)
        {
            restore_exception_handler();
        }
    }
    
    /**
     * Gets the complete output buffer and cleans it.
     *
     * @return string The complete output buffer (if any).
     *
     */
    protected function getAndCleanOutputBuffer()
    {
        $outputBuffer = '';
        
        while (ob_get_level())
        {
            $outputBuffer .= $ob_get_clean();
        }
        
        return $outputBuffer;
    }
}