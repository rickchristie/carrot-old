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
 * Dependency Registration File Paths.
 *
 * The front controller will require this file to load an array named
 * '$registrations'. This array stores dependency registration file paths for
 * each namespace (\Vendor\Namespace). The front controller will inject
 * this variable to DependencyInjectionContainer via constructor parameter.
 *
 * At runtime, if the DIC cannot find the registration file path from
 * this array, it will try to load _dicregistration.php at the namespace's
 * folder. Alternatively, if you have defined a custom dependency registration
 * file location, it will be loaded regardless of whether _dicregistration.php
 * exists or not.
 *
 * You can use this feature to overwrite the DIC configuration of each
 * library (even Carrot's core classes) without actually editing anything
 * inside the \vendors folder.
 * 
 * <code>
 * $registrations['\Vendor\Namespace'] = '/absolute/path/to/_dicregistration.php';
 * </code>
 *
 * The file name doesn't have to be '_dicregistration.php', it's just
 * for example. You can name it anything you want, place it anywhere
 * you want. We suggest you to place it inside /registrations, with this
 * name format:
 *
 * Vendor.Namespace.php
 *
 * So if you are editing the dependency registration file for Carrot's core
 * classes, we recommend you place it as '/registrations/Carrot.Core.php'.
 *
 */

$registrations['\Carrot\Core'] = __DIR__ . DIRECTORY_SEPARATOR . 'registrations' . DIRECTORY_SEPARATOR . 'Carrot.Core.php';