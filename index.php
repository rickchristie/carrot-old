<?php

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

$dic->session = array('Session', function($dic)
{
	return new Session($_SESSION);
});

$dic->response = array('Response', function($dic)
{
	return new Response();
});

$dic->url = array('URL', function($dic)
{
	return new URL
	(
		$_SERVER
	);
});

$dic->carrot = array('Carrot', function($dic)
{
	return new Carrot
	(
		$dic->request,
		$dic->session,
		$dic
	);
});

$dic->set_global('request');
$dic->set_global('carrot');
$dic->set_global('session');
$dic->set_global('response');
$dic->set_global('url');

$carrot = $dic->carrot;

echo '<pre>', var_dump($_SERVER), '</pre>';

exit;


/*
|---------------------------------------------------------------
| GET REQUIRED CONFIG VARIABLES
|---------------------------------------------------------------
*/

require($abspath . 'config.php');

if (!isset($config) or !is_array($config))
{
	exit('Configuration file must contain $config and it must be an array.');
}

if (!isset($dic) or !is_array($dic))
{
	exit('Configuration file must contain $dic and it must be an array.');
}

/*
|---------------------------------------------------------------
| DETERMINE DEFAULT CONFIG ITEMS
|---------------------------------------------------------------
*/

$default_protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
$default_domain = $_SERVER['SERVER_NAME'];
$default_path = str_ireplace('/index.php', '', $_SERVER['SCRIPT_NAME']);

// Add trailing slash to default path, if it doesn't have it
if (empty($default_path) or substr($default_path, -1) != '/')
{
	$default_path .= '/';
}

$default_configurations = array
(
	'abspath' => $abspath,
	'protocol' => $default_protocol,
	'domain' => $default_domain,
	'path' => $default_path
);

$required_configurations = array('abspath', 'protocol', 'domain', 'path', 'default_request_name');

$search_paths = array
(
	$abspath . ''
);

/*
|---------------------------------------------------------------
| CONSTRUCT CORE CLASS DIC CONFIG
|---------------------------------------------------------------
*/

$dic['Config'] = array
(
	array('Contents' => $config, 'Type' => 'Value'),
	array('Contents' => $default_configurations, 'Type' => 'Value'),
	array('Contents' => $required_configurations, 'Type' => 'Value')
);

$dic['Request'] = array
(
	array('Contents' => $_SERVER, 'Type' => 'Value'),
	array('Contents' => $_GET, 'Type' => 'Value'),
	array('Contents' => $_POST, 'Type' => 'Value'),
	array('Contents' => $_FILES, 'Type' => 'Value'),
	array('Contents' => $_COOKIE, 'Type' => 'Value'),
	array('Contents' => $_REQUEST, 'Type' => 'Value'),
	array('Contents' => $_ENV, 'Type' => 'Value')
);

$dic['Session'] = array
(
	array('Contents' => $_SESSION, 'Type' => 'Value')
);

/*
|---------------------------------------------------------------
| INSTANTIATE CONFIG, REQUEST, SESSION
|---------------------------------------------------------------
*/

$required = array('default_request_name');

$request = new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE, $_REQUEST, $_ENV);
$config = new Config($abspath, $default_configurations, $required_configurations);
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

//$carrot = new Carrot($request, $session, $config);

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