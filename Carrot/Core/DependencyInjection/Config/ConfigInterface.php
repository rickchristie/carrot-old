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
 * Injection configuration interface.
 *
 * The injection configuration object is responsible for
 * converting user configuration (which could be anything from
 * an XML format to array PHP configuration) into instances of
 * InjectorInterface.
 * 
 * The Container instance will call {@see getInjector()} to get
 * the injector for each object instance it needs.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection\Config;

use Carrot\Core\DependencyInjection\Reference,
    Carrot\Core\DependencyInjection\Injector\InjectorInterface;

interface ConfigInterface
{
    /**
     * Add an injector.
     *
     * Although each configuration object can have their own types
     * of configuration format, be it XML, JSON, or simple arrays, it
     * still have to allow the user to add injector instance
     * explicitly. This allows Carrot to still be able to use your
     * custom configuration object without being aware of your
     * implementation.
     *
     * As a convention, injector instances that are explicitly set
     * using this method must have the HIGHEST PRIORITY, that is, if
     * there is a conflict between explicitly set injector instance
     * and user configuration, injectors set using this method is
     * always used.
     * 
     * @param InjectorInterface $injector The injector to be added.
     *
     */
    public function addInjector(InjectorInterface $injector);
    
    /**
     * Get the injector for the given reference.
     * 
     * Must return an instance of InjectorInterface. The Container
     * object calls this method when it needs to instantiate an
     * object. Ideally, this method will read the user configuration
     * and generate an InjectorInterface instance for the container.
     * 
     * @param Reference $reference The reference whose injector we
     *        wanted to get.
     * @return InjectorInterface The injector instance for the given
     *         reference.
     *
     */
    public function getInjector(Reference $reference);
}