<?php

class Foo
{
	public function __construct($bar)
	{
		$this->bar = $bar;
	}
	
	public function call_bar_test()
	{
		// This will call Bar->test()
		// which in turn will call
		// Database->test()
		
		return $this->bar->test();
	}
}