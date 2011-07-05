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

namespace Carrot\SimpleDocs;

use Carrot\Core\Provider;

class ModelProvider extends Provider
{
    /**
     * @var array List of singleton configurations (case-sensitive).
     */
    protected $singletons = array('Sample');
    
    /**
     * Returns the instance for 'Sample' configuration.
     * 
     * @return Model
     *
     */
    public function getSample()
    {
        return new Model(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Storage');
    }
}