<?php

class Home
{	
	public function __construct($value_test, $object_test, $array_test)
	{
		$this->value_test = $value_test;
		$this->object_test = $object_test;
		$this->array_test = $array_test;
	}
	
	public function index()
	{
		echo 'Home controller called successfully';
		echo '<pre>', var_dump($this), '</pre>';
	}
}