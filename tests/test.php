<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

class Foo
{
    public function on()
    {
        echo 'Hello!';
    }
}

$foo = new Foo;
$foo->on();