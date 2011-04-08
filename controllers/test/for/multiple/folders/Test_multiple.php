<?php

class Test_multiple extends Controller
{
	// We load the Foo library, which in turn
	// has dependency on Database, Rules,
	// and Bar. Bar has dependency on
	// Config and Database.
	
	protected $library_dependencies = array
	(
		'Foo' => 'foo'
	);
	
	protected $view_dependencies = array
	(
		'Test_view' => 'test_view'
	);
	
	public function index()
	{
		// Let's call the view object,
		// it will call the Foo object method Foo::call_bar_test()
		// which calls Bar::test() which calls Database::test()
		
		$message = $this->foo->call_bar_test();
		
		// Display the message
		
		$this->test_view->render($message);
	}
}