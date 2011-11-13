<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Autoloader interface.
 *
 * This interface defines the contract between the Autoloader
 * and \Carrot\Core\Application. You can define your own
 * autoloader by implementing this interface.
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Autoloader;

interface AutoloaderInterface
{
    /**
     * Registers the autoloader function.
     *
     * Called by \Carrot\Core\Application after loading the
     * autoloader configuration file.
     *
     */
    public function register();
    
    /**
     * Returns an array of loaded class, along with the file path used.
     *
     * Useful for debugging. The method must return an array with
     * the class name as the index, and an information array as the
     * content. Example returned array structure:
     *
     * <code>
     * $loadedClass = array(
     *     'App\Blog\PostModel' => array(
     *         'time' => 1316930757,
     *         'path' => '/absolute/path/to/loaded/file.php'
     *     )
     * );
     * </code>
     *
     * @return array
     *
     */
    public function getLoadedFiles();
}