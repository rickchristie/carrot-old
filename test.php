<?php

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}

function exception_handler($exception)
{
    echo '<pre>', var_dump($exception), '</pre>';
}

set_error_handler("exception_error_handler");
set_exception_handler('exception_handler');

class Foo
{
    public function bar(Bar $bar)
    {
        
    }
}

$foo = new Foo;
$foo->bar('aaa');