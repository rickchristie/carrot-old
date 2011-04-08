<?php

/*
|---------------------------------------------------------------
| PROTOCOL
|---------------------------------------------------------------
| 
| Protocol, leave empty to guess. Otherwise fill with:
|
| 	https
|	http
|
|--------------------------------------------------------------- 
*/

$config['protocol'] = '';

/*
|---------------------------------------------------------------
| DOMAIN NAME OF THE SITE
|---------------------------------------------------------------
| 
| WITHOUT TRAILING SLASH. Leave empty to guess. Examples:
|
|	example.com
|	example.co.uk
|	www.example.com
|
|--------------------------------------------------------------- 
*/

$config['domain'] = '';

/*
|---------------------------------------------------------------
| PATH TO THE FRAMEWORK
|---------------------------------------------------------------
| 
| Path to the framework, WITH TRAILING SLASH. If framework is at
| the root of the domain, use '/'. Leave empty to guess.
| Examples:
|
|	/
|	/path/to/framework/
|
|--------------------------------------------------------------- 
*/

$config['path'] = '';

/*
|---------------------------------------------------------------
| DEFAULT CONTROLLER
|---------------------------------------------------------------
| 
| The controller and function to load when people are visiting
| the base URL. If the default controller function is left empty
| the framework assume it is the index() function.
|
|	$config['default_controller_name'] = 'Home';
|
| The controller name is NOT the controller's class name. If the
| controller is inside a folder, it includes the folder's name.
| For example, if our default controller's path is:
|
|	/path/to/framework/controllers/tour/features/Hardware.php
|
| The corresponding controller name will be:
|
|	tour/features/hardware
|
| With this setting the framework will call Hardware controller
| index() method. If you want to specify which method to be
| called, simply add the name of the method after the
| controller's name:
|
|	tour/features/hardware/method_name
|
|---------------------------------------------------------------
*/

$config['default_controller_name'] = 'test/for/multiple/folders/test_multiple';

/*
|---------------------------------------------------------------
| CUSTOM SET ROUTE CLASS
|---------------------------------------------------------------
| 
| If you want to have different rules in routing, create a
| library that has this method:
|
|	public function set_route()
|	{
|		...
|
|		return array
|		(
|			'controller_name' => 'same/format/as/default/controller/name',
|			'controller_class_name' => 'Settings',
|			'controller_path' => '/real/path/to/Settings.php',
|			'controller_method' => 'index',
|			'params' => array('foo', 'bar')
|		)
|	}
|
| As with all library, you can declare dependencies. The
| framework will have the Factory object instantiates your
| class, and the Router will call your method. Leave empty to
| use the default routing behavior. You can access uri segments
| and
|
| If you are unable to determine the controller, return the
| error message string and Router will load the 404 page.
|
|--------------------------------------------------------------- 
*/

$config['custom_set_route_class'] = '';

/*
|---------------------------------------------------------------
| MYSQL DATABASE CONFIGURATIONS
|---------------------------------------------------------------
| 
| When connecting, you can specify which connection to use:
|
|	$this->db->connect();
|	$this->db->connect('default');
|	$this->db->connect('statistics');
|
|--------------------------------------------------------------- 
*/

$config['db']['default']['dbhost'] = 'localhost';
$config['db']['default']['dbname'] = 'milestone';
$config['db']['default']['dbuser'] = 'root';
$config['db']['default']['dbpass'] = 'root';

/*
|---------------------------------------------------------------
| MAINTENANCE MODE
|---------------------------------------------------------------
| 
| When set to TRUE, all visits to this website will be be
| redirected to the maintenance template.
|
|--------------------------------------------------------------- 
*/

$config['maintenance'] = FALSE;
$config['maintenance_passphrase'] = 'luigi';

/*
|---------------------------------------------------------------
| PRODUCTION
|---------------------------------------------------------------
| 
| When set to TRUE, this framework will suppress all error
| messages. Otherwise it will display ALL error messages.
|
|--------------------------------------------------------------- 
*/

$config['live'] = FALSE;