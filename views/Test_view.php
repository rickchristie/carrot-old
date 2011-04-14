<?php

class Test_view
{
	public function __construct($foo)
	{
		$this->foo = $foo;
	}
		
	public function render($message)
	{		
		$this->template('header');
		
		?>
		
		<h1><?php echo htmlentities($message) ?></h1>
		
		<?php
		
		$this->template('footer');
	}
}