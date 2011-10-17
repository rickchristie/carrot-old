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
 * Load the configuration file. Most of configurations in Carrot
 * are done with dependency injection. The configuration values
 * loaded from the configuration file are the configuration
 * values that are needed before the injector is ready.
 *
 * Both index.php and config.php are the only files that need to
 * stay on the site's root. Other files can be moved, provided
 * that you write the paths correctly in config.php. The name of
 * this file (index.php) must not change, otherwise base path
 * guessing might not work correctly.
 *
 */

$config = require 'config.php';

/**
 * Require the file that contains Carrot\Core\Application class,
 * instantiate it and dispatch the request. Send the response
 * back to the client.
 * 
 */

require $config['files']['application'];
$app = new Carrot\Core\Application($config);
$app->initialize();
$response = $app->run();
$response->send();