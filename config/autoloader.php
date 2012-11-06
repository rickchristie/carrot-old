<?php

/**
 * Handles autoloading of PHP class files for the entire
 * application. Edit this file if you have a custom setup for
 * your files. Carrot assumes that after loading this file, all
 * class files will be autoloaded.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */

namespace Carrot\Framework;

/**
 * PSR-0 autoloader script, as written by the guys at PHP
 * Framework Interoperability Group.
 * 
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 */
function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName  = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
    $namespace = '';
    
    if ($lastNsPos = strripos($className, '\\'))
    {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    
    if (file_exists($fileName))
    {
        require $fileName;
    }
}

// Registers the autoloader.
spl_autoload_register('Carrot\Framework\autoload');