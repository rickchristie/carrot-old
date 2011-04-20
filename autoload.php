<?php

namespace Carrot;

spl_autoload_register(function ($class)
{
    if (substr($class, 0, strlen(__NAMESPACE__)) != __NAMESPACE__) {
        //Only autoload libraries from this package
        return;
    }
    
    //echo $class;
    
    $path = substr(str_replace('\\', '/', $class), strlen(__NAMESPACE__));
    
    echo $path;
    
    $path = __DIR__ . $path . '.php';
    
    //echo $path;
    
    if (file_exists($path)) {
        require $path;
    }
});

class Blah
{
	protected $data = 'blablah';
	
	public function set_data($string)
	{
		$this->data = $string;
	}
}

function dududu($obj)
{
	$obj->set_data('it works!');
}

$blah = new Blah();
dududu($blah);
echo '<pre>', var_dump($blah), '</pre>';