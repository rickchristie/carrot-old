<?php

require('../test.php');

$foo = $dic->foo;
$foo2 = $dic->foo;
echo '<pre>$foo = ', var_dump($foo), '</pre>';
echo '<pre>$foo2 = ', var_dump($foo2), '</pre>';