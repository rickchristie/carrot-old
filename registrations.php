<?php

/**
 * Dependency Registration File Paths.
 * 
 * Carrot's DependencyInjectionContainer will search the namespace folder
 * for '_dicregistration.php' as default behavior. You can specify a different
 * registration file to load using this configuration file.
 * 
 * <code>
 * $registrations['\Vendor\Namespace'] = '\absolute\path\to\_dicregistration.php';
 * </code>
 *
 * The file name doesn't have to be '_dicregistration.php', it's just
 * for example. You can name it anything you want, place it anywhere
 * you want.
 *
 */

//$registrations['\Carrot\Core'] = __DIR__ . DIRECTORY_SEPARATOR . 'registrations' . DIRECTORY_SEPARATOR . 'Carrot.Core.php';