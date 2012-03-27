<?php

$regex = '/^([A-Za-z\\\\_0-9]+)(@([A-Za-z_0-9]+))?(:(Singleton|Transient))?$/';

$test = array(
    'Carrot\MySQLi\MySQLi@Main:Singleton',
    'Carrot\MySQLi\MySQLi',
    'Carrot\MySQLi\MySQLi:Singleton',
    'Carrot\MySQLi\MySQLi:Transient',
    'Carrot\MySQLi\MySQLi@Main',
    'asdfas asdf asdf@asdf:adf'
);

foreach ($test as $string)
{
    $result = preg_match_all($regex, $string, $matches);
    echo '<pre>', var_dump($string), '</pre>';
    echo '<pre>', var_dump($result), '</pre>';
    echo '<pre>', var_dump($matches), '</pre>';
    echo '<hr />';
}