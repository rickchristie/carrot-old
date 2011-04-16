<?php

require('framework/core/DI_Container.php');

class Foo
{
	protected $bar;
	
	public function __construct($bar)
	{
		$this->bar = $bar;
	}
}

class Bar
{
	protected $data;
	
	public function __construct($data)
	{
		$this->data = $data;
	}
}

$dic = new DI_Container();

$dic->foo = array('Foo', function($dic)
{
	return new Foo
	(
		$dic->bar
	);
});

$dic->bar = array('Bar', function($dic)
{
	return new Bar
	(
		__DIR__
	);
});

$dic->choco_bar = array('Bar', function($dic)
{
	return new Bar
	(
		'Chocolate Bar'
	);
});

$dic->set_global('foo');