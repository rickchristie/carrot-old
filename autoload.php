<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Autoload behavior
 *
 * This autoload function ensures that if your class is properly namespaced,
 * it will be loaded automatically when needed. Your namespace must match
 * your class location. For example, the file /Foo/Bar/Baz.php must contain
 * Baz class within \Foo\Bar namespace.
 *
 */

spl_autoload_register(function($class)
{
	$class = ltrim($class, '\\');
	$path = '';
	$namespace = '';
	
	// Separate namespace with class, change namespace to path
	if ($last_namespace_pos = strripos($class, '\\'))
	{
		$namespace = substr($class, 0, $last_namespace_pos);
		$class = substr($class, $last_namespace_pos + 1);
		$path = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}
	
	$path .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
	
	if (file_exists($path))
	{
		require $path;
	}
});