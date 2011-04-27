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
 * Implementation adheres to the PSR-0 universal autoloader final proposal. Modified
 * from a sample found in: {@link http://groups.google.com/group/php-standards/web/psr-0-final-proposal?pli=1}
 *
 * If you want to put your classes in folder other than /vendors, simply add
 * another function to the spl_autoload_register list. This file is part of the
 * core, so it may be updated at the future, so remember to write your autoloader
 * functions after each update.
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
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'vendors' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    
    $path .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    
    if (file_exists($path))
    {
        require $path;
    }
});