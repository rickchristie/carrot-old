<?php

/**
 * Dependency injection configuration file.
 * 
 * Configure your dependencies here. You can also override
 * configuration for Carrot's core classes (previously defined in
 * index.php).
 * 
 * <code>
 * $dic->bind('App\Controller{Main:Transient}', array(
 *     new ObjectReference('App\Model{Main:Singleton}')
 * ));
 * </code>
 *
 * Since this is a configuration file and is not a inside any
 * class, you may use PHP superglobals.
 *
 * @see Carrot\Core\DependencyInjectionContainer
 * @see Carrot\Core\Interfaces\ProviderInterface
 *
 */

use Carrot\Core\ObjectReference;

$dic->bind('Sample\Route{Main:Transient}', array(
    new ObjectReference('Carrot\Core\AppRequestURI{Main:Transient}')
));