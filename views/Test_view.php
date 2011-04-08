<?php

class Test_view extends View
{	
	public function render($message)
	{		
		$this->template('header');
		
		?>
		
		<h1><?php echo htmlentities($message) ?></h1>
		
		<?php
		
		$this->template('footer');
	}
}