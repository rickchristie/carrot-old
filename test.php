<?php

$x = function ($string)
{
	$b = function ($string)
	{
		require (__DIR__ . '/test2.php');
		return $string;
	};
	
	$blah = $b($string);
	return $blah;
};

echo $x('blah');