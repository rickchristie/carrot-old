<?php

namespace Foo;

spl_autoload_register(function($className)
{
    echo '<pre>', var_dump($className), '</pre>';
});

$blah = new \Heyho\Blah();