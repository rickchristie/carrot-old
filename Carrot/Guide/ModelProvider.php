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
 * Provider for Model
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Guide;

use Carrot\Core\Provider;

class ModelProvider extends Provider
{   
    /**
     * Returns the instance for 'Main' configuration.
     * 
     * @return Model
     *
     */
    public function getMain()
    {
        return new Model(__DIR__ . DIRECTORY_SEPARATOR . 'Storage');
    }
}