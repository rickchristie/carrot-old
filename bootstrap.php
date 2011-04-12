<?php

/*
|---------------------------------------------------------------
| DECLARE DEPENDENCIES
|---------------------------------------------------------------
| 
| To install libraries, you can use this file to access the
| DI_container object ($dic), which then you can use to declare
| dependencies on objects:
|
|	$dic->set_dep
|
|--------------------------------------------------------------- 
*/

$dic->register('Auth', array($config->item('abspath'), '&Object' => 'Request', 'Object' => 'library'));
$dic->instantiate('Auth', '&');