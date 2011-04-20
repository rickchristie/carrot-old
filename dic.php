<?php

namespace Carrot;

/**
 * Defies imagination, extends boundaries and saves the world ...all before breakfast!
 *
 */

$dic->add_bundle('App', __DIR__ . '');

$dic->router = array('Router', function($dic)
{
	return new Router
	(
		
	);
});

$dic->error_handler = array('Error', function($dic)
{
	
});

$dic->maintenance_handler = array('', function($dic)
{
	
});

$dic->response = array('Response', function($dic)
{
	return new Response($dic->request->server('SERVER_PROTOCOL'));
});

$dic->session = array('Session', function($dic)
{
	return new Session($_SESSION);
});

/*
|---------------------------------------------------------------
| USER OBJECTS DEPENDENCIES
|---------------------------------------------------------------
| 
| 
| 
|--------------------------------------------------------------- 
*/

$dic->home_controller = array('Home_Controller', function($dic)
{
	return new Home_Controller
	(
		$dic->router->param(0),
		$dic->router->param(1)
	);
});