<?php

/**
 * SPL Autoload Register always gives you the fully qualified namespace
 * as the argument (without backslash prefix).
 *
 */

namespace Foo;

spl_autoload_register(function($className)
{
    echo '<pre>', var_dump($className), '</pre>';
});

$blah = new \Heyho\Blah();