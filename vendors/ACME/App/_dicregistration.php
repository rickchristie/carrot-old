<?php

$dic->register('\ACME\App\Controllers\HomeController:main', function($dic)
{
	return new \ACME\App\Controllers\HomeController
	(
		$dic->getInstance('\Carrot\Core\Classes\Request:shared')
	);
});