<?php

namespace Carrot\Framework;

/**
 * Define your routes here. You can access Carrot's router via
 * $router variable.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */

if (!isset($router) OR $router instanceof Router == FALSE)
{
    // Exit immediately if $router variable isn't set up.
    // This means this file is loaded directly.
    exit;
}