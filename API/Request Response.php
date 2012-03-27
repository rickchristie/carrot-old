<?php

/**
 * Request-Response Cycle.
 * 
 * All requests for Carrot must go through the index.php file.
 * There are four files that must be present at the root
 * directory of your application:
 * 
 * - index.php (front controller)
 * - autoloader.php (handles autoloading)
 * - config.php (main configuration file)
 * - .htaccess
 * 
 * Other files can be moved provided that you edit autoloader.php
 * and config.php accordingly.
 * 
 * The autoloader file handles autoloading for the entire
 * application. While config.php handles configuration for the
 * entire application.
 * 
 * The front controller will require autoloader.php, instantiate
 * configuration object, and load config.php to fill it with
 * values.
 * 
 * After that the front controller will instantiate the
 * Application object and run it.
 *
 */

/**
 * 
 *
 */