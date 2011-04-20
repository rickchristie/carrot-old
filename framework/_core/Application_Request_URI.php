<?php

/**
 * Application Request URI
 *
 * Copyright (c) 2011 Ricky Christie
 *
 * Licensed under the MIT License.
 * 
 * Represents the cleaned up Request URI. Used by Request class. For more
 * information on 
 * 
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 * @todo		
 */

class Application_Request_URI
{
	/**
	 * @var array 
	 */
	protected $path_segments;
	
	/**
	 * @var array comments
	 */
	protected $get;
	
	/**
	 * Defies imagination, extends boundaries and saves the world ...all before breakfast!
	 *
	 */
	public function __construct(array $path_segments, array $get)
	{
		$this->path_segments = $path_segments;
		$this->get = $get;
	}
	
	/**
	 * Defies imagination, extends boundaries and saves the world ...all before breakfast!
	 *
	 */
	public function segments()
	{
		return $this->path_segments;
	}
	
	/**
	 * Defies imagination, extends boundaries and saves the world ...all before breakfast!
	 *
	 */
	public function get()
	{
		return $this->get;
	}
}