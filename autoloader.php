<?php

/**
 * Autoloader.
 * 
 * You can use object or function, whichever you prefer. If you
 * use an object, be sure to return the object to Carrot so that
 * it can be saved.
 *
 */

require __DIR__ . DIRECTORY_SEPARATOR .
        'library' . DIRECTORY_SEPARATOR .
        'Carrot' . DIRECTORY_SEPARATOR .
        'Autoloader' . DIRECTORY_SEPARATOR .
        'Autoloader.php';

require __DIR__ . DIRECTORY_SEPARATOR .
        'library' . DIRECTORY_SEPARATOR .
        'Carrot' . DIRECTORY_SEPARATOR .
        'Autoloader' . DIRECTORY_SEPARATOR .
        'AutoloaderLog.php';

$autoloader = new Carrot\Autoloader\Autoloader;
$autoloader->bindNamespace('\\', __DIR__ . DIRECTORY_SEPARATOR . 'library');
$autoloader->bindNamespace('\\', __DIR__ . DIRECTORY_SEPARATOR . 'application');
$autoloader->register();
return $autoloader;