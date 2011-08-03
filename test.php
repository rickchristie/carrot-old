<?php

/**
 * Testing the database 
 *
 */

require __DIR__ . DIRECTORY_SEPARATOR . 'Carrot' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Autoloader.php';
$autoloader = new Carrot\Core\Autoloader;
$autoloader->bindNamespaceToDirectory('Carrot', __DIR__ . DIRECTORY_SEPARATOR . 'Carrot');
$autoloader->register();

$mysqli = new Carrot\Database\MySQLi(
    'localhost',
    'root',
    'root',
    'test2'
);




$reflection = new ReflectionClass('MySQLi_STMT');
echo '<pre>', var_dump($reflection->__toString()), '</pre>';
$method = new ReflectionMethod('MySQLi_STMT', '__construct');
echo '<pre>', var_dump($method->getParameters()), '</pre>';

exit;

$mysqli = new MySQLi(
    'localhost',
    'root',
    'root',
    'test2'
);

$stmt = new Carrot\Database\MySQLi\Statement($mysqli, 'SELECT * FROM account');

echo '<pre>', var_dump($s), '</pre>';

exit;


$mysqli->blah();
echo '<pre>', var_dump($mysqli), '</pre>';

exit;

// namespace Foo;

spl_autoload_register(function($className)
{
    echo '<pre>', var_dump($className), '</pre>';
});

$blah = new \Heyho\Blah();