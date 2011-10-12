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
 * Provider Interface
 * 
 * This is the interface you must implement when you are using
 * Carrot\Core\DependencyInjectionContainer. The provider acts as
 * a small factory, wiring the dependencies and/or configuration
 * by injecting it.
 *
 * Usually this wiring is done by using DIC's bind method, but if
 * you need to have logic in wiring the dependencies you can use
 * provider class to store that logic for you. This interface has
 * only one method, and that method must return the provided
 * object.
 * 
 * If your provider class has its own dependencies, don't forget
 * to bind it also using the DIC.
 *
 * For more information, please see the docs for
 * {@see Carrot\Core\DependencyInjectionContainer}.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

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