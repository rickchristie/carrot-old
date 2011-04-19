<?php

/*
|---------------------------------------------------------------
| CORE OBJECTS DEPENDENCIES
|---------------------------------------------------------------
| 
| 
| 
|--------------------------------------------------------------- 
*/

$dic->url = array('URL', function($dic)
{
	return new URL
	(
		$dic->request->server()
	);
});

$dic->router = array('Router', function($dic)
{
	return new Router
	(
		
	);
});

$dic->error = array('Error', function($dic)
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