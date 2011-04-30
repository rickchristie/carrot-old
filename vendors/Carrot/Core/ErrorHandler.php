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
 * Error Handler
 * 
 * Carrot's default error and exception handler class. It loads default templates
 * located at 'Templates' folder in this file's directory. You can replace the templates
 * with your own. Sets development_mode at constructor initialization.
 *
 * You can replace this class with your own error/exception handler by editing
 * config.php. Your class must implement \Carrot\Core\ErrorHandlerInterface.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

class ErrorHandler implements \Carrot\Core\Interfaces\ErrorHandlerInterface
{
    /**
     * @var bool TRUE if ErrorHandler::set() is called, FALSE otherwise.
     */
    protected $set = FALSE;
    
    /**
     * @var bool When set to TRUE, development error/exception page will be used instead, also affects error_reporting and display_errors.
     */
    protected $development_mode;
    
    /**
     * @var string Absolute path to the production error template. Used when ErrorHandler::development_mode is FALSE.
     */
    protected $error_template;
    
    /**
     * @var string Absolute path to the production uncaught exception template. Used when ErrorHandler::development_mode is FALSE. 
     */
    protected $exception_template;
    
    /**
     * @var string Absolute path to the development error template. Used when ErrorHandler::development_mode is TRUE.
     */
    protected $error_template_dev;
    
    /**
     * @var string Absolute path to the development uncaught exception template. Used when ErrorHandler::development_mode is TRUE.
     */
    protected $exception_template_dev;
    
    /**
     * Constructs the error handler.
     * 
     * @param string $server_protocol Either 'HTTPS 1.0' or 'HTTP 1.1', used to set the status code to 500.
     * @param bool $development_mode Optional. When set to TRUE, will use development error/exception templates, otherwise will use production error/exception templates.
     * @param string $error_template Optional. Absolute path to the production error template. Used when ErrorHandler::development_mode is FALSE.
     * @param string $exception_template Optional. Absolute path to the production uncaught exception template. Used when ErrorHandler::development_mode is FALSE. 
     * @param string $error_template_div Optional. Absolute path to the development error template. Used when ErrorHandler::development_mode is TRUE.
     * @param string $exception_template_div Optional. Absolute path to the development uncaught exception template. Used when ErrorHandler::development_mode is TRUE.
     *
     */
    public function __construct ($server_protocol, $development_mode = FALSE, $error_template = '', $exception_template = '', $error_template_dev = '', $exception_template_dev = '')
    {
        $development_mode = (bool) $development_mode;
        
        if ($development_mode)
        {
            error_reporting(E_STRICT | E_ALL);
            ini_set('display_errors', 1);
        }
        else
        {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
        
        $sep = DIRECTORY_SEPARATOR;
        $curdir = __DIR__;
        $this->server_protocol = $server_protocol;
        $this->development_mode = $development_mode;
        
        $this->error_template = $this->getDefaultIfEmpty($error_template, "{$curdir}{$sep}Templates{$sep}ErrorPage.php");
        $this->exception_template = $this->getDefaultIfEmpty($exception_template, "{$curdir}{$sep}Templates{$sep}ExceptionPage.php");
        $this->error_template_dev = $this->getDefaultIfEmpty($error_template_dev, "{$curdir}{$sep}Templates{$sep}ErrorPageDev.php");
        $this->exception_template_dev = $this->getDefaultIfEmpty($exception_template_dev, "{$curdir}{$sep}Templates{$sep}ExceptionPageDev.php");
    }
    
    /**
     * Set the error and exception handler to a method in this class.
     *
     * Additionally, it also marks the property ErrorHandler::set as TRUE.
     *
     */
    public function set()
    {
        set_error_handler(array($this, 'error_handler'));
        set_exception_handler(array($this, 'exception_handler'));
        $this->set = TRUE;
    }
    
    /**
     * Restore error and exception handler.
     *
     * Will only work if ErrorHandler::set property is TRUE.
     *
     */
    public function restore()
    {
        if ($this->set)
        {
            restore_error_handler();
            restore_exception_handler();
            
            $this->set = FALSE;
        }
    }
    
    /**
     * Error handler method.
     *
     * Carrot's default error handler. Will call error_log() and use PHP's default
     * error logger. Displays development error template when ErrorHandler::development_mode
     * is set to TRUE, otherwise will display production error template.
     * 
     * @param $err_number
     * @param $err_string
     * @param $err_file
     * @param $err_line
     * @param $err_context
     *
     */
    public function error_handler($err_number, $err_string, $err_file, $err_line, $err_context)
    {
        // Get and clear output buffer (if any)
        $output_buffer = '';
        
        while (ob_get_level())
        {
            $output_buffer .= ob_get_clean();
        }
        
        // Define an associative array of error string
        // in reality the only entries we should
        // consider are E_WARNING, E_NOTICE, E_USER_ERROR,
        // E_USER_WARNING and E_USER_NOTICE
        
        $error_type = array
        (
            E_ERROR              => 'Error',
            E_WARNING            => 'Warning',
            E_PARSE              => 'Parsing Error',
            E_NOTICE             => 'Notice',
            E_CORE_ERROR         => 'Core Error',
            E_CORE_WARNING       => 'Core Warning',
            E_COMPILE_ERROR      => 'Compile Error',
            E_COMPILE_WARNING    => 'Compile Warning',
            E_USER_ERROR         => 'User Error',
            E_USER_WARNING       => 'User Warning',
            E_USER_NOTICE        => 'User Notice',
            E_STRICT             => 'Runtime Notice',
            E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
        );
        
        $err_type = 'Unknown Error';
        
        if (array_key_exists($err_number, $error_type))
        {
            $err_type = $error_type[$err_number];
        }
        
        unset($error_type);
        
        // PHP doesn't log the errors when we have custom error handler
        // set, so we have to call error_log manually.
        
        if (ini_get('log_errors'))
        {
            error_log(sprintf("PHP %s:  %s in %s on line %d", $err_type, $err_string, $err_file, $err_line));
        }
        
        $variables = array
        (
            'err_type' => $err_type,
            'err_number' => $err_number,
            'err_string' => $err_string,
            'err_file' => $err_file,
            'err_line' => $err_line,
            'err_context' => $err_context,
            'output_buffer' => $output_buffer
        );
        
        unset($err_type);
        unset($err_number);
        unset($err_string);
        unset($err_file);
        unset($err_line);
        unset($err_context);
        unset($output_buffer);
        
        if (!headers_sent())
        {
            header($this->server_protocol . ' 500 Internal Server Error');
        }
        
        if ($this->development_mode)
        {
            require($this->error_template_dev);
        }
        else
        {
            require($this->error_template);
        }
        
        exit;
    }
    
    /**
     * Exception handler method.
     *
     * Carrot's default exception handler. Will call error_log() and use PHP's
     * default error logger. Displays development exception template when ErrorHandler::development_mode
     * is set to TRUE, otherwise will display production exception template.
     * 
     * @param Exception $exception
     * 
     */
    public function exception_handler($exception)
    {   
        // Get and clear output buffer (if any)
        $output_buffer = '';
        
        while (ob_get_level())
        {
            $output_buffer .= $ob_get_clean();
        }
        
        // PHP doesn't log the errors when we have custom exception
        // handler set, so we have to call error_log manually.
        
        if (ini_get('log_errors'))
        {
            error_log($exception->__toString());
        }
            
        if (!headers_sent())
        {
            header($this->server_protocol . ' 500 Internal Server Error');
        }
        
        if ($this->development_mode)
        {
            require($this->exception_template_dev);
        }
        else
        {
            require($this->exception_template);
        }
        
        exit;
    }
    
    /**
     * Destroys the ErrorHandler object.
     *
     * If ErrorHandler::set is TRUE, we have to restore error and
     * exception handler before destroying this object for good.
     *
     */
    public function __destruct()
    {
        if ($this->set)
        {
            restore_error_handler();
            restore_exception_handler();
            
            $this->set = FALSE;
        }
    }
    
    // ---------------------------------------------------------------
    
    /**
     * Returns default value if the user value is empty().
     *
     * @param mixed $user_value User defined value.
     * @param mixed $default Default value.
     * @return mixed Default value if the user defined value is empty, returns user defined value otherwise.
     *
     */
    protected function getDefaultIfEmpty($user_value, $default)
    {
        if (empty($user_value))
        {
            return $default;
        }
        
        return $value;
    }
    
    /**
     * Returns variable dump in a string, encoding it using htmlentities().
     *
     * @param mixed $var Variable to be dumped.
     * @return string The variable dump, safe for HTML output.
     *
     */
    protected function getVarDump($var)
    {
        ob_start();
        var_dump($var);
        return htmlentities(ob_get_clean());
    }
}