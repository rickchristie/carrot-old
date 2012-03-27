<?php

namespace Carrot\Autopilot;

require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

$context = new Context('Class:Carrot\Autopilot\TestHelpers\Foo');
echo '<pre>', var_dump($context->isClass()), '</pre>';