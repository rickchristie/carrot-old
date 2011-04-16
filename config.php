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
| Path to the framework, WITH TRAILING AND STARTING SLASH. If
| framework is at the root of the domain, use '/'. Leave empty
| to guess. Examples:
|
|	/
|	/path/to/framework/
|
|--------------------------------------------------------------- 
*/

$config['path'] = '';

/*
|---------------------------------------------------------------
| DEFAULT REQUEST NAME
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

$config['default_request_name'] = 'test/for/multiple/folders/test_multiple';

/*
|---------------------------------------------------------------
| CUSTOM SET ROUTE CLASS
|---------------------------------------------------------------
| 
| 
| 
|--------------------------------------------------------------- 
*/

$config['custom_set_route_class'] = '';

/*
|---------------------------------------------------------------
| MAINTENANCE MODE
|---------------------------------------------------------------
| 
| When set to TRUE, all visits to this website will be be
| redirected to the maintenance template. You can bypass the
| maintenance template by passing the passphrase via query
| string:
|
| 	http://www.example.com/?passphrase=value
|
| This framework will create a session variable for you, which
| would let you bypass every 
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

/*
|---------------------------------------------------------------
| DEPENDENCY INJECTION CONTAINER (DIC) DEPENDENCY LIST
|---------------------------------------------------------------
| 
| Place your dependency injection configuration below. The DIC
| configuration defines parameters to be passed to construct an
| object. If the parameter type is 'Object', DIC will pass an
| instance of the object. DIC will instantiate dependencies
| recursively.
|
| $dic['Class_name'] = array
| (
|	  array('Contents' => 'Some string value', 'Type' => 'Value'),
|	  array('Contents' => $object_ref, 'Type' => 'Value'),
|	  array('Contents' => array('pear', 'grape', 'lime'), 'Type' => 'Value'),
|	  array('Contents' => 'Class_name', 'Type' => 'Object'),
|	  array('Contents' => 'Class_name', 'Type' => 'Object:force')
| );
|
| The DIC configuration for core classes are defined
| automatically by the framework. You will be able to overwrite
| it, but you are strictly discouraged to do so unless you know
| what you are doing. List of this framework's core classes:
|
|	Carrot
|	DI_Container
|	Config
|	Request
|	Response
|	Router
|	Session
|
|--------------------------------------------------------------- 
*/

$dic['Database_MySQL'] = array
(
	0 => array('Contents' => 'localhost', 'Type' => 'Value'),
	1 => array('Contents' => 'milestone', 'Type' => 'Value'),
	2 => array('Contents' => 'root', 'Type' => 'Value'),
	3 => array('Contents' => 'root', 'Type' => 'Value')
);

/*
|---------------------------------------------------------------
| DIC LIST OF CLASSES WITH SINGLETON LIFECYCLE
|---------------------------------------------------------------
| 
| Classes listed here as singleton will never be instantiated
| twice. When the DIC found a class listed as a class that
| should be handled using singleton lifecycle, the
| 'Object:force' type is ignored.
|
| If by mistake, a class is listed in both transient and
| singleton class list, it will be treated as a transient class.
|
|--------------------------------------------------------------- 
*/

$dic_singletons = array();

/*
|---------------------------------------------------------------
| DIC LIST OF CLASSES WITH TRANSIENT LIFECYCLE
|---------------------------------------------------------------
| 
| Classes listed in as transient will not be stored inside the
| DIC's cache. This means if any class have dependency to a
| transient class, the dependency injected will always be a
| newly constructed object.
|
| If by mistake, a class is listed in both transient and
| singleton class list, it will be treated as a transient class.
| 
|--------------------------------------------------------------- 
*/

$dic_transients = array();