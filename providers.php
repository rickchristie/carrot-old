<?php

/**
 * Carrot providers configuration file.
 * 
 * Carrot's dependency injection container uses provider classes
 * to contain the object creation logic (which includes wiring the
 * dependencies). You may use this file to bind provider classes.
 * Access the DIC with $dic variable.
 *
 * Example uses of this file would be, wiring the provider classes
 * of Carrot's core classes so you can modify it without actually
 * editing the default provider classes (this is the recommended
 * way of editing core provider classes).
 *
 * <code>
 * $dic->bindProviderToClass('App\CoreProviders\ExceptionHandlerProvider', 'Carrot\Core\ExceptionHandler');
 * </code>
 *
 * @see Carrot\Core\DependencyInjectionContainer
 * @see Carrot\Core\Provider
 * @see Carrot\Core\Interfaces\ProviderInterface
 *
 */

