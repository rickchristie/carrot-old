<pre>
<?php

$args = array('foo', 'bar', 'baz');

$function = function($foo, $bar, $baz)
{
    $concat = $foo . $bar . $baz;
    return TRUE;
};

echo 'VF: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    $function($args[0], $args[1], $args[2]);
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "<br>";

echo 'CF: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    call_user_func_array($function, $args);
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "<br>";

echo 'SV: ';
$start = microtime(true);
for ($i = 0; $i < 1000000; $i++) {
    $count = count($args);
    switch ($count)
    {
        case 0:
            $function();
            continue;
        break;
        case 1:
            $function($args[0]);
            continue;
        break;
        case 2:
            $function($args[0], $args[1]);
            continue;
        break;
        case 3:
            $function($args[0], $args[1], $args[2]);
            continue;
        break;
        case 4:
            $function($args[0], $args[1], $args[2], $args[3]);
            continue;
        break;
        case 5:
            $function($args[0], $args[1], $args[2], $args[3], $args[4]);
            continue;
        break;
        case 6:
            $function($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
            continue;
        break;
        case 7:
            $function($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
            continue;
        break;
    }
}
$end = microtime(true);
$elapsed = $end - $start;
echo $elapsed, ' secs', "<br>";

?>
</pre>