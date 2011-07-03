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
 * Provider for HelloWorldController
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace HelloWorld\Controllers;

use Carrot\Core\Provider;

class HelloWorldControllerProvider extends Provider
{
    /**
     * @var array List of dependencies that this provider class needs.
     */
    protected $dependencies = array('response' => 'Carrot\Core\Response@Main');
    
    /**
     * Returns the instance for 'Main' configuration.
     * 
     * @return HelloWorldController
     *
     */
    public function getMain()
    {
        return new HelloWorldController($this->response);
    }
}