<?php

/*
|---------------------------------------------------------------
| PERFORM PHP CONFIGURATION CHECKS
| 
| This framework runs assuming certain PHP configurations are
| set. We have to quite if the required conditions are not
| present.
|
|---------------------------------------------------------------
*/

if (get_magic_quotes_gpc())
{
	exit('Magic quotes are on. Turn off magic quotes.');
}

if (ini_get('register_globals'))
{
	exit('Register globals are on. Turn off register globals.');
}

if (intval(substr(phpversion(), 0, 1)) < 5)
{
	exit('This framework requires PHP 5, please upgrade.');
}

/*
|---------------------------------------------------------------
| BOOTSTRAPPING
|---------------------------------------------------------------
*/

session_start();

// Output buffering is only used by the system to reset the buffer
// when an error or an exception occurs. This way we can drop what
// we are doing and load a nice clean error template whenever an
// error or uncaught exception occured

ob_start();

/*
|---------------------------------------------------------------
| LOAD CORE FILES
|---------------------------------------------------------------
*/

// Load configuration file
$abspath = dirname(__FILE__) . '/';
require($abspath . 'config.php');

// Load base classes
require($abspath . 'framework/core/Library.php');
require($abspath . 'framework/core/Controller.php');
require($abspath . 'framework/core/Model.php');
require($abspath . 'framework/core/View.php');

// Load core classes
require($abspath . 'framework/core/Factory.php');
require($abspath . 'framework/core/Config.php');
require($abspath . 'framework/core/Database.php');
require($abspath . 'framework/core/Router.php');
require($abspath . 'framework/core/Request.php');
require($abspath . 'framework/core/Session.php');

/*
|---------------------------------------------------------------
| INSTANTIATE CORE OBJECTS
|---------------------------------------------------------------
*/

$config = new Config($config, $abspath);
$db = new Database($config);
$request = new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE, $_REQUEST, $_ENV, $config);
$session = new Session($_SESSION, $config);
$router = new Router($config, $request);
unset($abspath);

/*
|---------------------------------------------------------------
| INSTANTIATE FACTORY
|---------------------------------------------------------------
*/

$factory = new Factory($config, $db, $router, $request, $session);

/*
|---------------------------------------------------------------
| DETERMINE THE ROUTE
| 
| If the user decides to make a custom set route object, Factory
| will instantiates the custom set route object and call the
| set_route() method.
|
|---------------------------------------------------------------
*/

$custom_set_route_class = $config->item('custom_set_route_class');

if (!empty($custom_set_route_class))
{
	// Instantiates the custom set route object
	$custom_set_route_obj = $factory->instantiate($custom_set_route_class);
	$router->set_custom_route($custom_set_route_obj);
}
else
{
	$router->set_route();
}

/*
|---------------------------------------------------------------
| INSTANTIATE THE CONTROLLER AND RUN THE METHOD
|---------------------------------------------------------------
*/

$controller_class_name = $router->get_controller_class_name();
$controller = $factory->instantiate($controller_class_name, 'controller');
$method = $router->get_controller_method();
$params = $router->get_params();
$params_count = count($params);

// Show 404 if method does not exist
if (!method_exists($controller, $method))
{
	$router->show_404("Unable to find method ({$method}) inside the controller ({$controller_class_name}).");
}

// Call using parameters, avoid using call_user_func_array() when possible
switch ($params_count)
{ 
	case 0: $controller->$method(); break; 
	case 1: $controller->$method($params[0]); break; 
	case 2: $controller->$method($params[0], $params[1]); break; 
	case 3: $controller->$method($params[0], $params[1], $params[2]); break; 
	case 4: $controller->$method($params[0], $params[1], $params[2], $params[3]); break; 
	case 5: $controller->$method($params[0], $params[1], $params[2], $params[3], $params[4]); break; 
	default: call_user_func_array(array($controller, $method), $params);  break; 
}