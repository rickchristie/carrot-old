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
 * Sample provider for SimpleDocs controller
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\SimpleDocs;

use Carrot\Core\Provider;

class ControllerProvider extends Provider
{
    /**
     * @var array List of dependencies that this provider class needs.
     */
    protected $dependencies = array
    (
        'response' => 'Carrot\Core\Response@Main',
        'view' => 'Carrot\SimpleDocs\View@Sample',
        'model' => 'Carrot\SimpleDocs\Model@Sample'
    );
    
    /**
     * Returns the instance for 'Sample' configuration.
     * 
     * @return HelloWorldController
     *
     */
    public function getSample()
    {
        return new Controller($this->view, $this->model, $this->response);
    }
}