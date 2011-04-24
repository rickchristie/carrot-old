<?php

namespace ACME\App\Controllers;

class HomeController
{
	protected $request;
	
	public function __construct($request)
	{
		$this->request = $request;
	}
	
	public function index()
	{
		$n = $this->bnnn();
		
		$blah = 'al;<strong>sdjfapsjdfapjsdl;fjassadljfhasldfhalshdfoeialasfkjhalskdfjhalsdhflkasdhflkajshdflkajhsdlfjhasdfkjhaslkdjfhalskdjhflkasjdhfajkshdflkjahsdlfkasdf';
		
		return new \Carrot\Core\Classes\Destination
		(
			'\ACME\App\Controllers\HomeController:main',
			'bnnn',
			array()
		);
		
		//trigger_error('Unable to create response, object not injected', E_USER_ERROR);
		
		//throw new \RuntimeException('Front controller error, expected \Carrot\Core\Interfaces\ResponseInterface instance from controller method return, got \'NULL\' instead. Destination history: (1. \ACME\App\Controllers\HomeController:main->index).');
	}
	
	
	public function bnnn()
	{
		return new \Carrot\Core\Classes\Destination
		(
			'\ACME\App\Controllers\HomeController:main',
			'index',
			array()
		);
		
		return 'ss';
	}
}