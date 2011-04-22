<?php

namespace App\Controllers;

class HomeController
{
	protected $request;
	
	public function __construct($request)
	{
		$this->request = $request;
	}
}