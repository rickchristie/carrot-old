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
 * Router Chain
 * 
 * Carrot's default router uses chain of responsibility design pattern (sort of) to
 * determine the destination. Each of the chain should contain a function that
 * returns a Destination instance or proceed to the next chain.
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class RouterChain
{
	/**
	 * @var array List of functions to iterate.
	 */
	protected $functions;
	
	/**
	 * @var int The function index currently active.
	 */
	protected $active_index = -1;
	
	/**
	 * Pass the parameters to the next chain of responsibility.
	 *
	 * @param mixed $request Preferably a request object.
	 * @param mixed $session Preferably a session object.
	 * @return mixed Returns what the anonymous function returns.
	 *
	 */
	public function next($request, $session)
	{
		++$active_index;
		
		if (!isset($this->functions[$active_index]) or !is_callable($this->functions[$active_index]))
		{
			return NULL;
		}
		
		return $this->functions[$active_index]($request, $session, $this);
	}
	
	/**
	 * Start the chain all over again. Preferably with a new request or session object.
	 *
	 * @param mixed $request Preferably a request object.
	 * @param mixed $session Preferably a session object.
	 * @return mixed Returns what the chain returns.
	 *
	 */
	public function rewind($request, $session)
	{
		$active_index = 0;
		return $this->next($request, $session)
	}
	
	/**
	 * Add a new function to the chain of responsibility.
	 *
	 * @param Closure $function Anonymous function that takes three parameters.
	 *
	 */
	public function add(\Closure $function)
	{
		$this->functions[] = $function;
	}
}