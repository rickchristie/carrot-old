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
 * Provider Interface
 * 
 * This is the interface you must implement when you are using
 * Carrot\Core\DependencyInjectionContainer. The provider acts as
 * a small factory, wiring the dependencies and/or configuration
 * by injecting it.
 *
 * Usually this wiring is done by using DIC's bind method, but if
 * you need to have logic in wiring the dependencies you can use
 * provider class to store that logic for you.
 * 
 * 
 * 
 * 
 * 
 * This interface defines the contract between your provider
 * classes with Carrot's dependency injection container. In order
 * to make provider class development easier, you can extend
 * Carrot\Core\Provider instead of re-implementing this interface
 * for every provider class.
 *
 * For more information, please see the docs for
 * {@see Carrot\Core\Interfaces\Provider} and
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
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     * 
     * @return mixed The instance of 
     *
     */
    public function get();
}