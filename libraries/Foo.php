<?php

class Foo extends Library
{
	protected $library_dependencies = array
	(
		'Database' => 'db',
		'Rules' => 'rules',
		'Bar' => 'bar'
	);
	
	protected $db;
	
	public function call_bar_test()
	{
		// This will call Bar->test()
		// which in turn will call
		// Database->test()
		
		return $this->bar->test();
	}
}