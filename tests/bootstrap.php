<?php


$autoloader = require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoloader.php';
$autoloader->bindNamespace('\\', __DIR__ . DIRECTORY_SEPARATOR . 'helpers');