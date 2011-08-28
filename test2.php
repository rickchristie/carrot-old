<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'Carrot' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Autoloader.php';
$autoloader = new Carrot\Core\Autoloader;
$autoloader->register();
require 'autoload.php';

fopen('\\:aaaa', 'w');

//$ref = new Carrot\Core\DIReference('asfda', 'asdfasdf', 'SINGLETOn');
//echo '<pre>', var_dump($ref), '</pre>';

$b = function(Carrot\Core\Request $request)
{
    
};

$reflection = new ReflectionFunction($b);
$params = $reflection->getParameters();
echo '<pre>', var_dump($params[0]->getClass()->getName()), '</pre>';

exit;

class Foo
{
    public function __construct()
    {
        
    }
}

$refB = new ReflectionMethod('Foo', '__construct');

$ref = new ReflectionMethod('Carrot\Form\FormView', '__construct');
echo '<pre>', var_dump($ref), '</pre>';
echo '<pre>', var_dump($ref->isConstructor()), '</pre>';
echo '<pre>', var_dump($ref->getParameters()), '</pre>';

$params = $ref->getParameters();
$form = $params[0];
$renderer = $params[1];

echo '<pre>', var_dump($form->getClass()), '</pre>';
$paramsB = $refB->getParameters();

echo '<pre>', var_dump($paramsB), '</pre>';