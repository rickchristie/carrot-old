<?php

namespace ACME\Site\Controllers;

class HomeController
{
    protected $request;
    
    public function __construct(Carrot\Core\Classes\Request $request)
    {
        $this->request = $request;
    }
    
	public function sample()
	{
	    // Create new response
	    $response = new \Carrot\Core\Classes\Response($this->request->getServer('SERVER_PROTOCOL'));
	    $string = "<p>Hello World! I'm using carrot! Here's a dump of request object:</p>";
	    
	    // Get some data using output buffering
	    ob_start();
	    var_dump($request);
	    $string .= ob_get_clean();
	    
	    // Set the body and return the response
	    $response->setBody($string);
	    return $response;
	}
    
}