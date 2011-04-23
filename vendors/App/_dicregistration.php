<?php

$dic->register('\App\Controllers\HomeController:main', function($dic)
{
	return new \App\Controllers\HomeController
	(
		$dic->getInstance('\Carrot\Core\Request:shared')
	);
});