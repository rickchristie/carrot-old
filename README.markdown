Carrot: Simple, Experimental PHP Framework (Unstable)
=====================================================

Carrot is an experimental framework that is created as a learning project. It quickly grows
from a CodeIgniter-like framework into another beast of its own. It uses dependency injection
container to instantiate all of the core classes and it uses anonymous functions heavily. Note
that it's still very unstable, with many changes to the core coming (and features planned).

This document should provide you with some information on how Carrot works. Since Carrot is
still 0.1, this document may change in time. Detailed documentation is in progress, meanwhile,
please download the source and play with it if you wanted to know more about Carrot.

Requirements
------------

- PHP 5.3+
- Apache web server (not tested in other web servers)

Framework design goals
----------------------

- Create a framework without using the keyword global or static. Fully recognize the
  dangerous nature of global state and avoid it at all costs.
- Relying on dependency injection container to manage the dependencies of user classes,
  thus eliminating the need for a global registry of object.
- Fully utilize PHP's new features by refusing to support outdated PHP installations.
  One of those features are anonymous functions, which are used extensively throughout
  the framework.
- Build and make use of decoupled classes, avoid inheritance whenever possible. This,
  for one, allows the user's controller class to be a plain old PHP object managed by
  the dependency injection container. You can even pick one of Carrot's core classes
  and use it as a standalone class.
- Make the core as small and focused as possible. Carrot's main job is to be the front
  controller, setting up the dependency injection container, instantiating the user's
  controller and getting a response from it. It does not know, much less dictate how
  the controller is getting the response.
- Allow the user to replace core classes of Carrot using their own class only by
  implementing an interface as a contract to the front controller, thereby creating
  an environment that does not disturb the user's programming routine.
- Continue the development by adding libraries, which are essentially just decoupled
  classes that are properly namespaced. Each library must not know that it is being
  used inside a framework.

How Carrot Works
----------------

Carrot is essentially just a `FrontController`, `Router`, `ErrorHandler` and
`DependencyInjectionContainer` (DIC). Here is a typical request cycle in Carrot:

- Request redirected to `index.php` by `.htaccess`, which instantiates the `FrontController`.
- The `FrontController` accepts `RouterInterface` and `ErrorHandlerInterface` at construction.
  It sets the error handler at construction.
- `FrontController::dispatch()` is called, which dispatches the request to your controller:
    - Uses `RouterInterface::getDestination()` to get the `Destination` (a value object).
    - The `Destination` instance contains the DIC item identifier of a controller class,
      the method to call, and arguments to pass in method call. 
    - This information is used to instantiate the appropriate controller class with its
      dependencies.
    - After instantiation, `FrontController` calls the method based on information provided
      from the `Destination` object, getting an instance of `ResponseInterface` as the return
      value.
- `ResponseInterface::send()` is called, sending the response to the browser.

Autoloading
-----------

Carrot uses [PSR-0 universal autoloader](http://groups.google.com/group/php-standards/web/psr-0-final-proposal?pli=1)
as default autoloader, however it does not mandate you to adhere to it. If you have classes that
doesn't follow PSR-0, simply add register another autoloading function at `/autoload.php`.
Carrot does not have a notion of 'packages' or 'bundles', everything is just a regular namespaced
class.

Dependency Injection Container
------------------------------

Carrot uses a modifed version of [Fabien Potencier's sample dependency injection container code](http://www.slideshare.net/fabpot/dependency-injection-with-php-53),
it uses anonymous functions to describe the creation of objects without actually creating it.
Each configuration/registration item in the DIC has an identifier (ID), which consists of the
fully qualified name of the class and a configuration name separated by `'@'` sign:

    \Fully\Qualifed\ClassName@config_name
    \Carrot\Core\Request@shared
    \Carrot\Core\FrontController@main

The existence of 'configuration name' allows us to create different dependency registration
of the same class:

    \Some\Lib\Database@main
    \Some\Lib\Database@localhost
    \Some\Lib\Database@statistics

This identifier is used to refer to a specific instantiation configuration when we are
getting the instance. We register the identifier along with anonymous functions that
returns the instance, which takes one parameter, the `$dic` instance itself:

    $dic->register('\Some\Lib\Database@main', function($dic)
    {
        return new \Some\Lib\Database
        (
            'localhost',
            'username',
            'password',
            'db_name'
        );
    });

We place the above registration snippet into registration files, which are assigned
to specific namespace/class names, since the above registration is for `\Some\Lib\Database@main`,
we assign the registration file to `\Some` namespace at `/registrations.php`:

    $registrations['\Some'] = '/absolute/path/to/registration/file.php'

Carrot's DIC will load configuration files only if they are needed, starting from
registration files assigned to the top level namespace down to the class name. For
example, when `$dic->getInstance('\Some\Lib\Database@main')` is called,
the DIC class will load these registration files (in order):

    $registrations['\Some']
    $registrations['\Some\Lib']
    $registrations['\Some\Lib\ClassName']

It will stop loading registration files when the item is found. If `\Some\Lib\ClassName@main`
is registered by the time `\Some\Lib` registration file is loaded, the DIC will stop loading
dependency registration files.

Since almost all Carrot's core classes are instantiated via the DIC, Carrot - as a framework -
does not have central configuration file at all. If you need to change the behavior of Carrot's
core classes, simply edit the dependency registration file for `\Carrot\Core`. A couple of things
you can do:

- Replace the `ErrorHandler` class injected to the `FrontController` with your own
  implementation of `ErrorHandlerInterface`.
- Replace the `Router` class injected to the `FrontController` with your own implementation
  of `RouterInterface`.
- Change the routing parameters of Carrot's default `Router` class.
- Change the error/exception templates loaded by Carrot's default `ErrorHandler` class.
- Change the location of `/routes.php` file loaded by Carrot's default `Router`.

Thus, in Carrot, you can modify the behavior of each core classes directly by injecting different
arguments at their construction. More detailed information about how Carrot's DIC works can be read
at [`DependencyInjectionContainer`'s source](https://github.com/rickchristie/Carrot/blob/master/vendors/Carrot/Core/DependencyInjectionContainer.php).

Routing
-------

Carrot supports two way routing by accepting two anonymous functions, one for *routing*,
the other for *reverse-routing*. Each route is named by a unique name. This name is then
referred to when you want to reverse-route inside a template.

*Routing* anonymous function takes a single parameter, which is an object that contains
routing parameters (`$params`). They are set at `Router`'s constructor. If the routing function
can decipher the current request (based on information got from routing parameters), it
must return an instance of `Destination`. If it can't decipher the current request, it returns
nothing. `Router` will call routing functions sequentially, so the earlier route always wins.

*Reverse routing* anonymous function takes two parameters. The first one is routing parameters,
the second one is array of additional arguments sent when you call `Router::generateURL()`.
Your reverse routing function *must* return a URL string.

Here is how a typical route defined in `/routes.php`:

    // Route:welcome
    // Translates {/} to WelcomeController::index()
    $router->addRoute
    (   
        'welcome',
        function($params)
        {   
            // We don't need to return any value at all if it's not our route.
            if (empty($params->uri_segments))
            {
                return new Destination('\Carrot\Core\Controllers\WelcomeController@main', 'index');
            }
        },
        function($params, $vars)
        {
            // Since it's a route to the home page, we simply return a relative path.
            return $params->request->getBasePath();
        }
    ); 

To reverse route use `Router::generateURL`:

    $router->generateURL('welcome', array('foo' => 'bar', 'baz' => 'quz'));

`Router::getDestination()` is called by the `FrontController` to get the destination for each
request. If it has exhausted its list of routes and there is still no match, it will return
a default no-matching-route destination. This default destination is set during router object
construction, but you can also set it at `/routes.php`:

    $router->setDestinationForNoMatchingRoute(new Destination('\Your\Custom\PageNotFoundController', 'method_name', array($args1, $args2)));

To modify the behavior of Carrot's default router class, look for its dependency registration
snippet. Read the [source code documentation](https://github.com/rickchristie/Carrot/blob/master/vendors/Carrot/Core/Router.php)
to find out what constructor parameters it has and what happens when you change them.

Quick Introduction
------------------

### Creating your controller

Let's assume you wanted to create a controller class `ACME\App\Controllers\FooController`
and you don't mind placing your class inside `/vendors`. Adhering to PSR-0 rules so our controller
can be loaded without specifying another autoloader function, create this file:

    /vendors/ACME/App/Controllers/FooController.php

Since the controller method has to return an implementation of `ResponseInterface`, we inject
it via the constructor. Notice that your controller doesn't have to extend or implement any
interface, the only requirement is to return `ResponseInterface` whenever its methods are
dispatched by the `FrontController`:

    <?php
    
    namespace ACME\App\Controllers;
    
    class FooController
    {
        protected $response;
        
        public function __construct(\Carrot\Core\Interfaces\ResponseInterface $response)
        {
            $this->response = $response;
        }
        
        public function index()
        {
            // Build the response body
            ob_start();
            echo 'This is a sample page';
            $response_body = ob_get_clean();
            
            // Return the response
            $this->response->setBody($response_body);
            return $this->response;
        }
    }

Voila, our controller is done.

### Registering controller's dependencies

Now that our controller is done, we need to register its dependencies in a registration
file. Say we want to keep dependency registrations of all controllers in one file, first
we create the file:

    /registrations/ACME.App.Controllers.php

and write the registration snippet in it:

    <?php
    
    // Register FooController's dependencies
    $dic->register('\ACME\App\Controllers\FooController@main', function($dic)
    {
        return new \ACME\App\Controllers\FooController
        (
            // Use DIC to get object dependencies
            // Here we use Carrot's default Response class
            $dic->getInstance('\Carrot\Core\Response@main');
        );
    });

finally, we assign the registration file to `\ACME\App\Controllers`. Open
`/registrations.php` and add this line:

    $registrations['\ACME\App\Controllers'] = __DIR__ . DIRECTORY_SEPARATOR . 'registrations' . DIRECTORY_SEPARATOR . 'ACME.App.Controllers.php';

Our controller class is now managed by the dependency injection container.

### Adding a route that points to our controller

Our controller can't be accessed by the user if there is no route pointing to it. To
create a route using Carrot's default `Router`, edit `/routes.php` and create the route
using `Router::addRoute()`:

    // Route:foo
    // Translates {/foo} to FooController::index()
    $router->addRoute
    (
        'foo',
        function($params)
        {
            // Return an instance of Destination that points to
            // FooController if the segment is /foo - use the DIC
            // identifier we just created to refer to our controller.
            
            if (isset($params->uri_segments[0]) && $params->uri_segments[0] == 'foo')
            {
                return new Destination('\ACME\App\Controllers\FooController@main', 'index');
            }
        },
        function($params, $vars)
        {
            return $params->request->getBasePath() . 'foo/';
        }
    );

That's it! Requests for `http://hostname/base/path/foo` should be dispatched to `FooController::index()`!

Feedback
--------

The author welcomes any healthy criticisms and/or feedback. Criticisms are the main catalyst
of development. Please send them all to [seven dot rchristie at gmail dot com](mailto:seven.rchristie@gmail.com).

Credits
-------

This framework uses a modifed version of [Fabien Potencier's sample dependency injection container code](http://www.slideshare.net/fabpot/dependency-injection-with-php-53).
If you haven't already, make sure you read the slides, it's awesome! This framework also borrows many
ideas from Symfony and Zend Framework.

Special Thanks
--------------

[The people at Stack Overflow PHP chat](http://chat.stackoverflow.com/rooms/11/php), especially
@Gordon, @edorian, @ircmaxell, @markus, @zerkms - for their help and criticisms.