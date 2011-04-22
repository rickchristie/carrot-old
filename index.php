<?php

/**
 * Front Controller
 * 
 * Here is a short version of what the Front Controller does:
 *
 *    1. Bootstrap PHP.
 *    2. Create a dependency injection container.
 *    3. Constructs the error/exception handling class and assigns them.
 *    4. Constructs the Router object (custom or default).
 *    5. Get the destination from Router.
 *    6. Instantiates the controller and runs the method.
 *    7. Get the response object from the method and send it to the client.
 *
 * You can edit config.php to replace DefaultRouter and DefaultErrorHandler
 * with your own custom classes.
 *
 */

/**
 * This framework runs assuming certain PHP conditions are set
 * We have to quit if the required conditions aren't met.
 *
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

/**
 * Starts session, we are going to need $_SESSION
 * to instantiate session classes.
 *
 */
 
session_start();

/**
 * 
 *
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'registrations.php';

/**
 * If values are not set by the user, replace the variables
 * with the framework's default variables.
 *
 */

if (!isset($registrations) or !$registrations)
{
	$registrations = array();
}

if (!isset($router) or !$router)
{
	$router = '\Carrot\Core\Classes\Router:main';
}

if (!isset($error_handler) or !$error_handler)
{
	$error_handler = '\Carrot\Core\Classes\ErrorHandler:shared';
}

/**
 * Instantiates dependency injection container.
 */

$dic = new Carrot\Core\Classes\DependencyInjectionContainer(__DIR__, $registrations);

/**
 * Instantiates the error handler.
 *
 */

$error_handler = $dic->getInstance($error_handler);
$error_handler->set();

/**
 * Defies imagination, extends boundaries and saves the world ...all before breakfast!
 *
 */

$router = $dic->getInstance($router);
$request = $dic->getInstance('\Carrot\Core\Classes\Request:shared');
//$destination = $router->getDestination();

echo '<pre>', var_dump($request->getAppRequestURISegments()), '</pre>';

exit;

/**
 * Defies imagination, extends boundaries and saves the world ...all before breakfast!
 *
 */

$temp = $destination;
$count = 0;

while (is_a($temp, '\Carrot\Core\Classes\Destination'))
{	
	if (++$count >= 10)
	{
		throw new RuntimeException('Front Controller error in getting Response, too much controller redirection.');
	}
	
	// Try to get an instance of the controller. If failed
	// (i.e. DIC fails to get the instance, or the method
	// doesn't exist), use default router 404 destination
	// 
	
	try
	{
		$controller = $dic->getInstance($temp->getControllerDICRegistrationID());
		//if (method_exists($controller, $temp->))
	}
	catch (\Exception $e)
	{
		
	}
	
	//if ()
}