<?php

/**
 * Carrot PHPUnit bootstrap file.
 * 
 * Loads the autoloader file.
 *
 */

namespace Carrot\Test;

require dirname(__DIR__) . DIRECTORY_SEPARATOR .
        'config' . DIRECTORY_SEPARATOR .
        'autoloader.php';

/**
 * PSR-0 autoloader script, as written by the guys at PHP
 * Framework Interoperability Group.
 * 
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 */
function autoloadUnit($className)
{
    $className = ltrim($className, '\\');
    $fileName  = __DIR__ . DIRECTORY_SEPARATOR . 'Unit' . DIRECTORY_SEPARATOR;
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

// Register autoloaders.
spl_autoload_register('Carrot\Test\autoloadUnit');