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
 * Provider for View
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\SimpleDocs;

use Carrot\Core\Provider;

class ViewProvider extends Provider
{
    /**
     * @var array List of dependencies that this provider class needs.
     */
    protected $dependencies = array
    (
        'model' => 'Carrot\SimpleDocs\Model@Sample',
        'router' => 'Carrot\Core\Router@Main'
    );
    
    /**
     * Returns the instance for 'Sample' configuration.
     * 
     * @return View
     *
     */
    public function getSample()
    {
        return new View($this->model, $this->router, 'Sample');
    }
}