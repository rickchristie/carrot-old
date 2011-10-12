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
 * Dependency Injector Provider Interface
 * 
// ---------------------------------------------------------------
 * The provider acts as a small factory, wiring the dependencies
 * and/or configuration by injecting it.
 *
 * TODO: Finish documentation
 *
 * For more information, please see the docs for
 * {@see DependencyInjector}.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection;

interface ProviderInterface
{
    /**
     * Returns the provided object.
     * 
     * @return Object The provided object.
     *
     */
    public function get();
}