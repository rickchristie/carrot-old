<?php

require('framework/core/DI_Container.php');

$dic = new DI_Container();

$dic->ab = array('Blah', function($dic)
{
	return new Blah();
});

$blah = $dic->ab;