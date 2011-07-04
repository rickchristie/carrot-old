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
 * Provider for Controller
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Guide;

use Carrot\Core\Provider;

class ControllerProvider extends Provider
{
    /**
     * @var array List of dependencies that this provider class needs.
     */
    protected $dependencies = array
    (
        'response' => 'Carrot\Core\Response@Main',
        'view' => 'Carrot\Guide\View@Main',
        'model' => 'Carrot\Guide\Model@Main'
    );
    
    /**
     * Returns the instance for 'Main' configuration.
     * 
     * @return HelloWorldController
     *
     */
    public function getMain()
    {
        return new Controller($this->view, $this->model, $this->response);
    }
}