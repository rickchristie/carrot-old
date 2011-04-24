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
 * Load configuration files.
 *
 * > autoload.php - Registers autoloading functions.
 * > config.php - Loads router and error handler class DIC item ID.
 * > registrations.php - Loads DIC registration file paths.
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
 * Instantiates dependency injection container and set the error
 * and exception handler.
 *
 */

$dic = new Carrot\Core\Classes\DependencyInjectionContainer(__DIR__ . DIRECTORY_SEPARATOR . 'vendors', $registrations);
$error_handler = $dic->getInstance($error_handler);
$error_handler->set();

/**
 * Defines a couple of functions to be used. So that we don't
 * pollute the environment with global functions, we define
 * anonymous functions instead.
 *
 */

$checkDestination = function($destination, $error_message)
{
	if (!is_a($destination, '\Carrot\Core\Classes\Destination'))
	{
		if (is_object($destination))
		{
			$type = get_class($destination);
		}
		else
		{
			$type = gettype($destination);
		}
		
		throw new \RuntimeException(sprintf($error_message, $type));
	}
};

/**
 * Instantiates router and gets the destination from Router.
 * At this point, the destination could be valid or invalid.
 * Front controller will make sure that it returns a valid
 * Destination object.
 *
 */

// Get variables
$router = $dic->getInstance($router);
$router->loadRoutesFile(__DIR__ . DIRECTORY_SEPARATOR . 'routes.php');
$destination = $router->getDestination();

// Check destination
$checkDestination($destination, "Front controller error when getting destination from Router. Expected an instance of \Carrot\Core\Classes\Destination from Router, got '%s' instead.");

/**
 * Loop through the response from the user controller. If the
 * controller returns an instance of Destination, do an internal
 * redirection. If it's not, then assume it's a response and
 * proceed to sending it.
 *
 */

$temp = $destination;
$internal_redirection_count = 0;
$having_no_matching_route_as_destination = FALSE;
$destination_history = '';

while (is_a($temp, '\Carrot\Core\Classes\Destination'))
{
	++$internal_redirection_count;
	$destination_history .= " ({$internal_redirection_count}. {$temp->getControllerDICItemID()}:{$temp->getMethodName()})";
	
	if ($internal_redirection_count > 10)
	{
		throw new \RuntimeException("Front controller error, too many internal redirections, possibly an infinite loop. Destination history:{$destination_history}.");
	}
	
	// If class doesn't exist, change destination to 'no matching route'
	if (!class_exists($temp->getClassName()))
	{
		// If we are using the no matching route destination, throw exception
		if ($having_no_matching_route_as_destination)
		{
			throw new \RuntimeException("Front controller error, class not found when attempting to use 'having no matching route' destination. Class does not exist ({$temp->getClassName()}). Destination history:{$destination_history}.");
		}
		
		$having_no_matching_route_as_destination = TRUE;
		$temp = $router->getDestinationForNoMatchingRoute();
		$checkDestination($temp, "Front controller error when getting 'destination for no matching route' from Router. Expected an instance of \Carrot\Core\Classes\Destination from Router, got '%s' instead. Destination history:{$destination_history}.");
		continue;
	}
	
	// Instantiate controller
	$controller = $dic->getInstance($temp->getControllerDICItemID());
	
	// If method doesn't exist, change destination to 'no matching route'
	if (!method_exists($controller, $temp->getMethodName()))
	{
		// If we are using the no matching route destination, throw exception
		if ($having_no_matching_route_as_destination)
		{
			throw new \RuntimeException("Front controller error, class not found when attempting to use 'having no matching route' destination. Method doesn't exist ({$temp->getMethodName()}). Destination history:{$destination_history}");
		}
		
		$having_no_matching_route_as_destination = TRUE;
		$temp = $router->getDestinationForNoMatchingRoute();
		$checkDestination($temp, "Front controller error when getting 'destination for no matching route' from Router. Expected an instance of \Carrot\Core\Classes\Destination from Router, got '%s' instead. Destination history:{$destination_history}.");
		continue;
	}
	
	// Run the method using call_user_func
	$temp = call_user_func_array(array($controller, $temp->getMethodName()), $temp->getParams());
}

// We just got our response
$response = $temp;
unset($temp);

// Check if it's a valid response
if (!is_a($response, '\Carrot\Core\Interfaces\ResponseInterface'))
{
	if (is_object($response))
	{
		$type = get_class($response);
	}
	else
	{
		$type = gettype($response);
	}
	
	throw new \RuntimeException("Front controller error, expected \Carrot\Core\Interfaces\ResponseInterface instance from controller method return, got '{$type}' instead. Destination history:{$destination_history}.");
}

// Send the response to the client
$response->send();