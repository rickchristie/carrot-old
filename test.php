<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'Carrot' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Autoloader.php';
$autoloader = new Carrot\Core\Autoloader;
$autoloader->register();
require 'autoload.php';

$chain = new Carrot\Validation\ValidationChain;
$chain->setParameters(array('username' => ''));

$chain->start('username')->validate('existence.notEmpty')->stop();

echo '<pre>', var_dump($chain->passesValidation()), '</pre>';
echo '<pre>', var_dump($messages = $chain->getMessages()), '</pre>';

$messages[0]->setParameterLabels(array('username' => 'User Name'));

echo '<pre>', var_dump($messages[0]->get()), '</pre>';

exit;

/**
 * SPL Autoload Register always gives you the fully qualified namespace
 * as the argument (without backslash prefix).
 *
 */

//namespace Foo;

spl_autoload_register(function($className)
{
    echo '<pre>', var_dump($className), '</pre>';
});

$blah = new \Heyho\Blah();