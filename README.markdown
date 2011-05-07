Carrot: Simple, Experimental PHP Framework (Unstable)
=====================================================

Carrot is an experimental framework that is created as a learning project. It quickly grows
from a CodeIgniter-like framework into another beast of its own. It uses dependency injection
container to instantiate all of the core classes and it uses anonymous functions heavily. Note
that it's still very unstable, with many changes to the core coming (and features planned).

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
Carrot does not have a notion of 'packages' or 'bundles', everything is a namespaced class.

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
returns the instance, the function takes one parameter, which is the `$dic` instance
itself: 

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
example, if `$dic->getInstance('\Namespace\Subnamespace\ClassName@main')` is called,
the DIC class will load these registration files (in order):

    $registrations['\Namespace']
    $registrations['\Namespace\Subnamespace']
    $registrations['\Namespace\Subnamespace\ClassName']

It will stop loading registration files if the item is found. More detailed information
about how `Carrot\Core\DependencyInjectionContainer` works can be read at the
[source code documentation](https://github.com/rickchristie/Carrot/blob/master/vendors/Carrot/Core/DependencyInjectionContainer.php).

Routing
-------

*in progress*


Quick Introduction
------------------

### Creating your controller

Let's assume you wanted to create a controller class `ACME\App\Controllers\HomeController`
and you don't mind placing your class inside `/vendors`. According to PSR-0 rules, create
this file:

    /vendors/ACME/App/Controllers/HomeController.php

Since the controller method has to return an implementation of `ResponseInterface`, we inject
it via the constructor. Notice that your controller doesn't have to extend or implement any
interface, the only requirement is to return `ResponseInterface` whenever its methods are
dispatched by the `FrontController`:

```php
<?php

namespace ACME\App\Controllers;

class HomeController
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
        
        $this->response->setBody($response_body);
        return $this->response;
    }
}
```

Voila, our controller is done.

### Registering controller's dependencies

Now that our controller is done, we need to register its dependencies in a registration
file. Say we want to keep dependency registrations of all controllers in one file, first
we create the file:

    /registrations/ACME.App.Controllers.php

and write the registration snippet in it:

```php
// Register HomeController's dependencies
$dic->register('\ACME\App\Controllers\HomeController@main', function($dic)
{
    return new \ACME\App\Controllers\HomeController
    (
        // Use DIC to get object dependencies
        // Here we use Carrot's default Response class
        $dic->getInstance('\Carrot\Core\Response@main');
    );
});
```

finally, we assign the registration file to `\ACME\App\Controllers`. Open
`/registrations.php` and add this line:

    $registrations['\ACME\App\Controllers'] = __DIR__ . DIRECTORY_SEPARATOR . 'registrations' . DIRECTORY_SEPARATOR . 'ACME.App.Controllers.php';

Our controller class is now managed by the dependency injection container.

### Adding a route that points to our controller

*working on this section*

Feedback
--------

The author welcomes any healthy criticisms and/or feedback. Criticisms are the main catalyst
of development. Send them all to [seven dot rchristie at gmail dot com](mailto:seven.rchristie@gmail.com).

Credits
-------

This framework uses a modifed version of [Fabien Potencier's sample dependency injection container code](http://www.slideshare.net/fabpot/dependency-injection-with-php-53).
If you haven't already, make sure you read the slides, it's awesome! It also borrows many ideas
from Symfony and Zend Framework.

Special Thanks
--------------

[The people at Stack Overflow PHP chat](http://chat.stackoverflow.com/rooms/11/php), especially
@Gordon, @edorian, @ircmaxell, @markus, @zerkms - for their help, criticisms, and guidance.