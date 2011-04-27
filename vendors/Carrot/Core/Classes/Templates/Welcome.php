<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Welcome to Carrot, an experimental PHP framework</title>
	<style>
		body, html
		{
			font-family: 'Helvetica', 'Arial', sans-serif;
			margin: 0;
			padding: 0;
			font-size: 13px;
		}
		
		.grey
		{
			color: #666;
		}
		
		code
		{
			font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace;
			font-size: 90%;
		}
		
		pre
		{
			font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace;
			font-size: 12px;
			display: block;
			overflow: auto;
			background: #edf0f3;
			border: 1px solid #cbd3db;
			line-height: 17px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			padding: 15px;
		}
		
		.code-container
		{
			font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace;
			font-size: 12px;
			display: block;
			overflow: auto;
			background: #edf0f3;
			border: 1px solid #cbd3db;
			line-height: 15px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			overflow: hidden;
			padding: 0;
			margin: 15px 0;
		}
		
		.code-container ol > li:last-child span
		{
			border-bottom-left-radius: 5px;
			-moz-border-radius-bottomleft: 5px;
		}
		
		.code-container ol > li:last-child pre
		{
			border-bottom-right-radius: 5px;
			-moz-border-radius-bottomright: 5px;
		}
		
		.code-container ol > li:first-child span
		{
			border-top-left-radius: 5px;
			-moz-border-radius-topleft: 5px;
		}
		
		.code-container ol > li:first-child pre
		{
			border-top-right-radius: 5px;
			-moz-border-radius-topright: 5px;
		}
		
		.code-container ol
		{
			margin: 0;
			padding: 0;
			list-style-type: none;
		}
		
		.code-container ol li
		{
			margin: 0;
			padding: 0 0 0 0px;
		}
		
		.code-container ol li span.line-num
		{
			display: block;
			float: left;
			margin: 0;
			padding: 4px 6px;
			background: #e1eeff;
			border-right: 1px solid #cbd3db;
			width: 25px;
			text-align: right;
			overflow: hidden;
		}
		
		.code-container ol li.current span.line-num
		{
			background: #fe6b6b;
			border-right: 1px solid #fe6b6b;
			color: #fff;
		}
		
		.code-container ol li pre
		{
			margin: 0;
			padding: 4px 0 4px 0px;
			width: auto;
			overflow: hidden;
			background: transparent;
			border: none;
			line-height: 15px;
			-moz-border-radius: 0px;
			border-radius: 0px;
		}
		
		.code-container ol li.odd pre
		{
			background: #f5f8fb;
		}
		
		.code-container ol li.current pre
		{
			background: #fe6b6b;
			color: #fff;
		}
		
		.stacktrace
		{
			font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace;
			font-size: 12px;
			margin: 20px 0;
		}
		
		.stacktrace .filename
		{
			background: #f5f9fb;
			border-left: 1px solid #cbd3db;
			border-right: 1px solid #cbd3db;
			border-top: 1px solid #cbd3db;
			border-bottom: 1px solid #cbd3db;
			padding: 10px;
			border-top-right-radius: 5px;
			-moz-border-radius-topright: 5px;
			border-top-left-radius: 5px;
			-moz-border-radius-topleft: 5px;
		}
		
		.stacktrace .funcname
		{
			background: #edf0f3;
			padding: 10px;
			border-left: 1px solid #cbd3db;
			border-right: 1px solid #cbd3db;
			border-bottom: 1px dotted #cbd3db;
		}
		
		.stacktrace .args pre
		{
			margin: 0;
			padding: 10px;
			border-top: none;
			border-top-right-radius: 0px;
			-moz-border-radius-topright: 0px;
			border-top-left-radius: 0px;
			-moz-border-radius-topleft: 0px;
			max-height: 300px;
		}
		
		#wrapper
		{
			width: 650px;
			margin-left: 127px;
			margin-right: 80px;
			margin-top: 30px;
			margin-bottom: 120px;
		}
		
		#header
		{
			width: 650px;
			margin-left: 80px;
			margin-top: 80px;
		}
		
		a:link
		{
			color: #f13900;
			text-decoration: none;
		}
		
		a:visited
		{
			color: #ff5825;
			text-decoration: none;
		}
		
		a:hover
		{
			color: #ff843a;
			text-decoration: none;
			border-bottom: 1px solid #ccc;
		}
		
		a:active
		{
			color: #f13900;
			text-decoration: none;
		}
		
		p
		{
			font-size: 13px;
			line-height: 20px;
			margin: 15px 0;
		}
		
		h2
		{
			font-size: 19px;
		}
		
		h3
		{
			font-size: 17px;
		}
		
		h1, h2, h3, h4, h5, h6
		{
			font-weight: normal;
			margin: 20px 0;
		}
		
		h1
		{
			width: 166px;
			height: 112px;
			position: relative;
			overflow: hidden;
		}
		
		h1 span
		{
			display: block;
			position: absolute;
			width: 100%;
			height: 100%;
			background: url(data:image/png;base64,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);
			background-repeat: no-repeat;
		}
		
		ul
		{
			margin: 0 0 0 25px;
			padding: 0 0 0 0;
			list-style-type: none;
			font-size: 13px;
			line-height: 20px;
		}
		
		ul li
		{
			margin: 15px 0;
			padding: 0 0 0 22px;
			background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAaRJREFUeNqc0l8oQ1EcB/DvuXfW7v4gWY3JrNGWPKiJlELhDSnlAU8elPLiyYNXj548+fO49qgm7/IkL1LTZCzNljQMubYx9x6/W1eNdGd+dfqdfuf26Xd+5zLzqh9amCxV+C1el6IwCkHPzfhnfAH2j0JxGxyLtA9WApj0HLOo5lCxoGwxizABhonwcaKG6s/6Mu6gVrF7q7hpn3Heai4ogyvxm5hk5kk6SkZmrMNlgSdRvhIhBGxcwkL6DqPVucaRPqDbp2hdRAgJlpsBZNvb+VQm09Mvy+iQFCoADU0iOj2qpCMuQ4CGiKHHh2ies9vUJRXShDyp8LgFBFzcrSPST4Cuzb8V6KNeSnvt7x91bfV05qI524HTayBxzzbHQ7l5Q0BHtP9ix5dXgh02le4iAg6GoxTD7SvGCdk1BHREa3fDIyuzLSYVtU4BGUnAYYZlqd5GSNYQKIHmKK0FZKXGb+U4s4qIF1iYgOk/ATqivcC6K6dOermKC5uIe7ABQg7+BJRAYxrke1GbEw7hhPZdFQEls1mitUxrvmKgBHJSyn8KMAD+06jQNTPITwAAAABJRU5ErkJggg==);
			background-repeat: no-repeat;
			background-position: 0px 0px;
		}
		
	</style>
</head>
<body>
<div id="header">
	<h1>
		<span></span>
		Carrot
	</h1>
</div>
<div id="wrapper">
	<h2>Welcome to Carrot, an experimental PHP framework!</h2>
	
	<p>
		This is a response returned from <code>Carrot\Core\Classes\SampleController::welcome()</code>,
		it should mean that Carrot is working okay at your server. You can learn how Carrot works at
		a glance simply by reading the introduction below, or you can read a detailed introduction
		at <a href="http://carrot.rickchristie.com">Carrot's main site</a>.
	</p>
	
	<h3>Framework design goals</h3>
	
	<ul>
	   <li>
	       Create a framework without using the keyword <code>global</code> or <code>static</code>. Fully recognize the
	       dangerous nature of global state and avoid it at all costs.
	   </li>
	   <li>
	       Relying on dependency injection container to manage the dependencies of user classes, thus eliminating the need
	       for a global registry of object.
	   </li>
	   <li>
	       Fully utilize PHP's new features by refusing to support outdated PHP installations. One of those features
	       are anonymous functions, which are used extensively throughout the framework.
	   </li>
	   <li>
	       Build and make use of decoupled classes, avoid inheritance whenever possible. This, for one, allows the user's
	       controller class to be a plain old PHP object managed by the dependency injection container. You can even
	       pick one of Carrot's core classes and use it as a standalone class.
	   </li>
	   <li>
	       Make the core as small and focused as possible. Carrot's only job is to be the front controller, setting up the
	       dependency injection container, instantiating the user's controller and getting a response from it. It does
	       not know, much less dictate <em>how</em> the controller is getting the response.
	   </li>
	   <li>
	       Allow the user to replace core classes of Carrot using their own class only by implementing an interface as
	       a contract to the front controller, thereby creating an environment that does not disturb the user's
	       programming routine.
	   </li>
	   <li>
	       Continue the development by adding libraries, which are essentially just decoupled classes that are properly
	       namespaced. Each library <em>must not know</em> that it is being used inside a framework.
	   </li>
	</ul>
	
	<h3>Creating your own controller</h3>
	<p>
		Carrot uses the universal autoloader as defined in the <a href="http://groups.google.com/group/php-standards/web/psr-0-final-proposal?pli=1">PSR-0 Final Proposal</a>,
		which means your controller have at least two namespace (<code>Vendor\Namespace</code>) and properly placed. For starters,
		let's create the controller class <code>ACME\Site\Controllers\HomeController</code>. Create this file:
	</p>
	<pre><?php echo htmlspecialchars($root_directory), DIRECTORY_SEPARATOR, 'ACME', DIRECTORY_SEPARATOR, 'Site', DIRECTORY_SEPARATOR, 'Controllers', DIRECTORY_SEPARATOR, 'HomeController.php' ?></pre>
	
	<p>
		When you create your controller in Carrot, remember that each of its method that got called by the front controller
		has a responsibility to return an implementation of <code>Carrot\Core\Interfaces\ResponseInterface</code>.
		How your controller class gets the response is none of the front controller's business.
	</p>
	
	<p>
		We are going to need an instance of <code>Request</code>, so we inject it via the constructor:
	</p>
	
	<pre>&lt;?php

namespace ACME\Site\Controllers;

class HomeController
{
    protected $request;
    
    public function __construct(\Carrot\Core\Classes\Request $request)
    {
        $this->request = $request;
    }
}</pre>
	
	<p>
		You can create your own response class by implementing <code>ResponseInterface</code>, but for now let's stick to the default
		that Carrot provides. Let's create a method in the controller that uses <code>Request</code> to create a simple response:
	</p>
	
	<pre>public function sample()
{
    // Create new response
    $response = new \Carrot\Core\Classes\Response($this->request->getServer('SERVER_PROTOCOL'));
    $string = "&lt;p&gt;Hello World! I'm using carrot! Here's a dump of request object:&lt;/p&gt;";
    
    // Get some data using output buffering
    ob_start();
    echo '&lt;pre&gt;', var_dump($this->request), '&lt;/pre&gt;';
    $string .= ob_get_clean();
    
    // Set the body and return the response
    $response->setBody($string);
    return $response;
}
</pre>
	
	<p>
		Voila, we just created a simple controller class. Now we need to tell <code>DependencyInjectionContainer</code> to inject
		a <code>Request</code> instance when constructing <code>HomeController</code>. We do this by <em>registering</em> the dependencies
		of <code>HomeController</code> via a dependency registration file.
	</p>
	
	<h3>Registering dependencies to the dependency injection container</h3>
	
	<p>
		Carrot's default <code>DependencyInjectionContainer</code> (DIC) uses registration files to store DIC configuration. Each
		namespace (<code>Vendor\Namespace</code>) have their own registration file. If no custom registration
		file path is defined for the namespace in question, the DIC will try to find this <code>_dicregistration.php</code> at the namespace's folder.
		Since our newly created controller's namespace is <code>ACME\Site</code> and we didn't specify a custom registration
		file path at <code>/registrations.php</code>, DIC will try to load this file when the front controller gets an instance
		of our controller:
	</p>
	
	<pre><?php echo htmlspecialchars($root_directory), DIRECTORY_SEPARATOR, 'ACME', DIRECTORY_SEPARATOR, 'Site', DIRECTORY_SEPARATOR, '_dicregistration.php' ?></pre>
	
	<p>
		Note that you don't need to create this file if you don't want your class to be managed by the DIC. You only need to
		do this if your class <em>has a dependency that needs to be injected by the default DIC object</em>. If your class doesn't have
		a dependency registration file/item, when <code>DependencyInjectionContainer::getInstance()</code> is called it will try to
		construct an instance of your class without construction parameters. The usage of DIC allows your controller to be a Plain Old
		PHP Object without any restriction other than its responsibility to return a <code>ResponseInterface</code> implementation for
		each method called by the front controller.
	</p>
	
	<p>
		We use <code>DependencyInjectionContainer::register()</code> to register an anonymous function inside the registration file.
		The anonymous function must return an instance of the class registered. We have to create a DIC identification according to this syntax:
	</p>
	
	<pre>\Vendor\Namespace\Subnamespace\...\ClassName@registration_name</pre>
	
	<p>
		The point of having a registration name is that it allows you to register two instances of the same class. So if you need more than one
		configuration for <code>ACME\Lib\Database</code> class you can register two different DIC item with different identifications:
	</p>
	
	<pre>\ACME\Lib\Database@main_db
\ACME\Lib\Database@logging_db</pre>
	
	<p>
		In our anonymous function, we tell <code>DependencyInjectionContainer</code> to get an instance of the <code>Carrot\Core\Classes\Request</code>
		using the DIC registration ID of the core request object. You can find out the core classes' DIC identification simply by opening its
		<code>_dicregistration.php</code> file. Here is our registration code:
	</p>
	
	<pre>&lt;?php

$dic->register('\ACME\Site\Controllers\HomeController@main', function($dic)
{
    return new \ACME\Site\Controllers\HomeController
    (
        $dic->getInstance('\Carrot\Core\Classes\Request@shared')
    );
});</pre>

	<p>
		While our controller is finished, it doesn't mean that the front controller will automatically call it. After creating
		a controller, we need to tell <code>Router</code> to translate a route to our controller method, namely
		<code>HomeController::sample()</code>.
	</p>
	
	<h3>Adding a route that points to the controller we just created</h3>
	
	<p>
		Carrot's default <code>Router</code> uses a simplified version of the chain of responsibility pattern. For each route,
		we create an anonymous function that either returns an instance of <code>Destination</code> or pass that responsibility
		and arguments to the next function in the chain. Your anonymous function must accept three arguments, <code>$request</code>,
		<code>$session</code>, and <code>$router</code> itself.
	</p>
	
	<p>
		We will add a route for <code>http://<?php echo htmlentities($http_host . $base_path) ?>sample</code>. Open <code>/routes.php</code>
		and add the code below:
	</p>
	
	<pre>// Translates {/site} to HomeController::sample()
$router->add(function($request, $session, $router)
{
    $app_request_uri = $request->getAppRequestURISegments();

    if (isset($app_request_uri[0]) && strtolower($app_request_uri[0]) == 'sample')
    {
        return new Destination
        (
            '\ACME\Site\Controllers\HomeController@main',
            'sample'
        );
    }

    return $router->next($request, $session, $router);
});</pre>

	<p>
		The <code>Destination</code> class is a simple class that denotes a <em>destination</em>. A destination is defined as
		the controller DIC registration ID, the method to call, and the arguments to pass to the method (optional). The front
		controller uses this object to instantiate your controller and call its method. Now you can visit
		<a href="http://<?php echo htmlentities($http_host . $base_path) ?>sample">your newly defined route</a> and get
		the response that we've been working on.
	</p>
	
	<h3>That's Carrot at a glance, we hope you like it!</h3>
	
	<p>
		The author welcomes any healthy critcisms, suggestions, and (especially) patches. Send them all to
		<a href="mailto:seven.rchristie@gmail.com">seven.rchristie@gmail.com</a>. Special thanks to Fabien
		Potencier for his awesome dependency injection slide and the people
		at <a href="http://chat.stackoverflow.com/rooms/11/php">Stack Overflow PHP Chat</a>, especially Gordon,
		ircmaxell, edorian, markus, zerkms, without them this thing wouldn't get done, so I'm gonna put their nick
		here even though they protest.
	</p>
</div>
</body>
</html>