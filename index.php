<?php

namespace Carrot;

/**
 * Defies imagination, extends boundaries and saves the world ...all before breakfast!
 *
 */
 
require('autoload.php');

/*
|---------------------------------------------------------------
| PERFORM PHP CONFIGURATION CHECKS
|---------------------------------------------------------------
| 
| This framework runs assuming certain PHP configurations are
| set. We have to quit if the required conditions are not met.
|
|---------------------------------------------------------------
*/

if (get_magic_quotes_gpc())
{
	exit('Magic quotes are on. Please turn off magic quotes.');
}

if (ini_get('register_globals'))
{
	exit('Register globals are on. Please turn off register globals.');
}

if (floatval(substr(phpversion(), 0, 3)) < 5.3)
{
	exit('This framework requires PHP 5, please upgrade.');
}

/*
|---------------------------------------------------------------
| START SESSION
|---------------------------------------------------------------
*/

session_start();

/*
|---------------------------------------------------------------
| START DEPENDENCY INJECTION CONTAINER
|---------------------------------------------------------------
*/

require(__DIR__ . '/framework/core/DI_Container.php');

$dic = new DI_Container();
$dic->add_search_path(__DIR__ . '/framework/');
$dic->add_search_path(__DIR__ . '/framework/core/');
$dic->add_search_path(__DIR__ . '/classes/');

/*
|---------------------------------------------------------------
| WRITE DEPENDENCY INJECTION CONFIG
|---------------------------------------------------------------
*/

require(__DIR__ . '/dic.php');

$dic->request = array('Request', function($dic)
{
	return new Request
	(
		$_SERVER,
		$_GET,
		$_POST,
		$_FILES,
		$_COOKIE,
		$_REQUEST,
		$_ENV
	);
});

$dic->carrot = array('Carrot', function($dic)
{
	return new Carrot($dic);
});

$dic->set_global('carrot');
$dic->set_global('session');

$carrot = $dic->carrot;
$response = $carrot->dispatch();
$response->send();