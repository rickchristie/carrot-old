<?php

class Bar extends Library
{
	protected $library_dependencies = array
	(
		'Config' => 'config',
		'Database' => 'db'
	);
	
	public function test()
	{
		return $this->db->test();
	}
}