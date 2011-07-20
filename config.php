<?php

/**
 * Carrot dependency injection configuration file.
 * 
 * Carrot 
 * 
 * <code>
 * 
 * </code>
 *
 * @see Carrot\Core\DependencyInjectionContainer
 * @see Carrot\Core\Interfaces\ProviderInterface
 *
 */

use Carrot\Core\ObjectReference;

$dic->bind('Sample\Route{Main:Transient}', array(
    new ObjectReference('Carrot\Core\AppRequestURI{Main:Transient}')
));

$dic->bind('Sample\Welcome{Main:Transient}', array(
    new ObjectReference('Carrot\Core\Router{Main:Singleton}'),
    new ObjectReference('Carrot\Core\Request{Main:Transient}')
));