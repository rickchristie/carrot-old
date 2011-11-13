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
 * Provider interface.
 *
 * The provider interface for provider classes to be used to
 * instantiate ProviderClassInjector.
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\DependencyInjection\Injector;

interface ProviderInterface
{
    /**
     * Get the provided object instance.
     * 
     * @return mixed The provided object instance.
     *
     */
    public function get();
}