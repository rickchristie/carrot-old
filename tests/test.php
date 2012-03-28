<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

class Bar
{
    
}

class Foo
{
    public $bar;
    
    public $blah;
    
    /**
     * Constructor.
     *
     */
    public function __construct(Bar $bar = NULL, $blah = NULL)
    {
        $this->bar = $bar;
        $this->blah = $blah;
    }
}

$class = new ReflectionClass('Foo');
$ctor = new ReflectionMethod('Foo', '__construct');

$params = $ctor->getParameters();
echo '<pre>', var_dump($params), '</pre>';

foreach ($params as $param)
{
    
    
    echo '<pre>', var_dump($param->isDefaultValueAvailable()), '</pre>';
    
    if ($param->isDefaultValueAvailable())
    {
        echo '<pre>', var_dump($param->getDefaultValue()), '</pre>';
    }
}

$foo = new Foo(new Bar, NULL);
echo '<pre>', var_dump($foo), '</pre>';