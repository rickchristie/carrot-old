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
 * Carrot Error Handler
 * 
 * asdf
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class ErrorHandler implements \Carrot\Core\Interfaces\ErrorHandlerInterface
{
	protected $set = FALSE;
	
	public function set()
	{
		$this->set = TRUE;
	}
	
	public function restore()
	{
		if ($this->set)
		{
			restore_error_handler();
			restore_exception_handler();
			
			$this->set = FALSE;
		}
	}
	
	public function error_handler()
	{
		
	}
	
	public function exception_handler()
	{
		
	}
	
	public function __destruct()
	{
		if ($this->set)
		{
			restore_error_handler();
			restore_exception_handler();
			
			$this->set = FALSE;
		}
	}
}