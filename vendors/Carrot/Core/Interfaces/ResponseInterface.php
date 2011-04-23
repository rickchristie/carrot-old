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
 * the front controller. It's quite straightforward, since the front controller
 * will only run one method.
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace \Carrot\Core\Interfaces;

interface ResponseInterface
{
	/**
	 * Sends (echoes out) the response to the client.
	 *
	 */
	public function send();
}