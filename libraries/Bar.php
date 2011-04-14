<?php

class Bar
{	
	public function __construct($db)
	{
		$this->db = $db;
	}
	
	public function test()
	{
		return $this->db->test();
	}
}