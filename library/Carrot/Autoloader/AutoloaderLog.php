<?php

namespace Carrot\Autoloader;

/**
 * Used for logging autoloading data. Might be useful for
 * debugging purposes.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class AutoloaderLog
{
    /**
     * @var array Autoloading log data.
     */
    private $data = array();
    
    /**
     * Logs the loaded class file for debug information.
     *
     * @param string $className Fully qualified class name.
     * @param string $filePath Absolute path to the class file loaded.
     *
     */
    public function log($className, $filePath)
    {
        $this->data[$className] = array(
            'time' => time(),
            'path' => $filePath
        );
    }
}