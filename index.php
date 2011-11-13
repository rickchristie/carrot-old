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
 * Load the configuration file.
 * 
 * This file (index.php) is the only file that must stay in the
 * site's root. Other files can be moved, provided you write the
 * paths correctly in config.php and this file. The name of this
 * file (index.php) must not change, otherwise base path guessing
 * will not work.
 *
 */

$config = require __DIR__ . DIRECTORY_SEPARATOR .
                  'config' . DIRECTORY_SEPARATOR .
                  'config.php';

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