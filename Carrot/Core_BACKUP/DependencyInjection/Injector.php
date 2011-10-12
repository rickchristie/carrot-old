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
 * Dependency Injector
 * 
 * Reads configuration from DIConfig instance given and injects
 * dependencies recursively to create the object.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection;

use RuntimeException;

class Injector
{   
    /**
     * @var DIConfig Represents dependency injector configuration.
     */
    protected $config;
    
    /**
     * Constructor.
     * 
     * The DIConfig instance will used must be already filled with
     * configurations.
     * 
     * <code>
     * $injector = new DependencyInjector($DIConfig);
     * </code>
     * 
     * @param DIConfig $config Represents dependency injector configuration.
     *
     */
    public function __construct(DIConfig $config)
    {
        $this->config = $config;
    }
    
    /**
     * Get an instance of the reference given.
     * 
    // ---------------------------------------------------------------
     * 
     * 
     * We use a regular loop here instead of recursion since PHP does
     * not support tail recursion and will crash if the recursion is
     * 100-200 levels deep.
     * 
     * @throws RuntimeException
     * @return mixed The instance asked.
     * 
     */
    public function getInstance(DIReference $reference)
    {
        
    }
}