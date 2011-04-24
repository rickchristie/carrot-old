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
 * Default error and exception handler used by Carrot. You can replace this
 * with your own error/exception handler class by editing /config.php. Your error
 * handler class must implement the ErrorHandlerInterface.
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class ErrorHandler implements \Carrot\Core\Interfaces\ErrorHandlerInterface
{
	/**
	 * @var bool TRUE if ErrorHandler::set() is called, FALSE otherwise.
	 */
	protected $set = FALSE;
	
	/**
	 * @var bool When set to TRUE, development error/exception page will be used instead.
	 */
	protected $display_errors;
	
	/**
	 * @var string Absolute path to the production error template. Used when ErrorHandler::display_errors is FALSE.
	 */
	protected $error_template;
	
	/**
	 * @var string Absolute path to the production uncaught exception template. Used when ErrorHandler::display_errors is FALSE. 
	 */
	protected $exception_template;
	
	/**
	 * @var string Absolute path to the development error template. Used when ErrorHandler::display_errors is TRUE.
	 */
	protected $error_template_dev;
	
	/**
	 * @var string Absolute path to the development uncaught exception template. Used when ErrorHandler::display_errors is TRUE.
	 */
	protected $exception_template_dev;
	
	/**
	 * Constructs the error handler.
	 * 
	 * @param string $server_protocol Either 'HTTPS 1.0' or 'HTTP 1.1', used to set the status code to 500.
	 * @param bool $display_errors When set to TRUE, will use development error/exception templates, otherwise will use production error/exception templates.
	 * @param string $error_template Absolute path to the production error template. Used when ErrorHandler::display_errors is FALSE.
	 * @param string $exception_template Absolute path to the production uncaught exception template. Used when ErrorHandler::display_errors is FALSE. 
	 * @param string $error_template_div Absolute path to the development error template. Used when ErrorHandler::display_errors is TRUE.
	 * @param string $exception_template_div Absolute path to the development uncaught exception template. Used when ErrorHandler::display_errors is TRUE.
	 *
	 */
	public function __construct ($server_protocol, $display_errors = FALSE, $error_template = '', $exception_template = '', $error_template_dev = '', $exception_template_dev = '')
	{
		$display_errors = (bool) $display_errors;
		
		if ($display_errors)
		{
			error_reporting(E_STRICT | E_ALL);
		}
		else
		{
			error_reporting(0);
		}
		
		$this->server_protocol = $server_protocol;
		$this->display_errors = $display_errors;
		$this->error_template = $this->getDefaultIfEmpty($error_template, __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'ErrorPage.php');
		$this->exception_template = $this->getDefaultIfEmpty($exception_template, __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'ExceptionPage.php');
		$this->error_template_dev = $this->getDefaultIfEmpty($error_template_dev, __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'ErrorPageDev.php');
		$this->exception_template_dev = $this->getDefaultIfEmpty($exception_template_dev, __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'ExceptionPageDev.php');
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
	 * error logger. Displays production error template when ErrorHandler::display_errors
	 * is set to FALSE, otherwise will display development error template.
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
			$output_buffer .= $ob_get_clean();
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
		
		if ($this->display_errors)
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
	 * default error logger. Displays production exception template when ErrorHandler::display_errors
	 * is set to FALSE, otherwise will display development exception template.
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
		
		if ($this->display_errors)
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
	
	protected function getDefaultIfEmpty($value, $default)
	{
		if (empty($value))
		{
			return $default;
		}
		
		return $value;
	}
	
	protected function getVarDump($var)
	{
		ob_start();
		var_dump($var);
		return htmlspecialchars(ob_get_clean());
	}
}