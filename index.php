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

if (floatval(substr(phpversion(), 0, 3)) < 5.3)
{
	exit('This framework requires PHP 5, please upgrade.');
}

/*
|---------------------------------------------------------------
| START SESSION AND OUTPUT BUFFER
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

$abspath = dirname(__FILE__) . '/';
require($abspath . 'framework/core/DI_Container.php');
require($abspath . 'framework/core/Config.php');
require($abspath . 'framework/core/Router.php');
require($abspath . 'framework/core/Request.php');
require($abspath . 'framework/core/Session.php');
require($abspath . 'framework/core/Carrot.php');
require($abspath . 'framework/core/Response.php');

/*
|---------------------------------------------------------------
| INSTANTIATE CONFIG, REQUEST, SESSION
|---------------------------------------------------------------
*/

$config = new Config($abspath);
$request = new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE, $_REQUEST, $_ENV);
$session = new Session($_SESSION);
unset($abspath);

/*
|---------------------------------------------------------------
| INSTANTIATE CARROT OBJECT.
|
| Carrot will take $config, $request, and $session as its
| instantiation parameter. 
|
|---------------------------------------------------------------
*/

$carrot = new Carrot($request, $session, $config);

/*
|---------------------------------------------------------------
| INSTANTIATE ROUTER
|---------------------------------------------------------------
*/

$router = new Router($request->server('REQUEST_URI'), $config->item('abspath') . 'controllers/', $config->item('default_request_name'), $config->item('path'));
$router->set_route();

//echo '<pre>', var_dump($router), '</pre>';

$carrot = new Carrot($request, $session, $config);

exit;


$search_paths = array
(
	$abspath . 'controllers'
);

$dic = new DI_container();

$blah = array
(
	$abspath,
	'blah',
	'Object' => '',
	array('blah')
);

$blah = array($abspath, 'blah', 'Object' => 'Auth');

//$dic->register('Foo', array())

echo '<pre>', var_dump($blah), '</pre>';

foreach ($blah as $index => $content)
{
	if (is_integer($index))
	{
		echo 'Integer <br />';
	}
	else if (is_string($index))
	{
		echo 'String <br />';
	}
}

exit();

$config = new Config($config, $abspath);
$request = new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE, $_REQUEST, $_ENV, $config);
$session = new Session($_SESSION, $config);
$router = new Router($config, $request);
unset($abspath);

$config->item('blah');



throw new InvalidArgumentException('blah!'); 

echo '<pre>', var_dump($blah), '</pre>';

exit();

/*
|---------------------------------------------------------------
| INSTANTIATE DI CONTAINER
|---------------------------------------------------------------
*/

$factory = new Factory($config, $router, $request, $session);

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