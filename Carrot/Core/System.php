<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>
 *
 * Licensed under the MIT License.
 *
 */

/**
 * System
 * 
 * This class is the bells and whistles of Carrot's framework.
 * It's refactored index.php file, so this class is sort of the
 * actor that initializes and calls everything.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use ErrorException;
use RuntimeException;

class System
{
    /**
     * @var string Absolute file path to DIC configuration file.
     */
    protected $configFilePath;
    
    /**
     * @var string Absolute file path to the EventDispatcher configuration file.
     */
    protected $eventsFilePath;
    
    /**
     * @var string Absolute file path to router configuration file.
     */
    protected $routesFilePath;
    
    /**
     * @var string Absolute file path to autoloader configuration file.
     */
    protected $autoloadFilePath;
    
    /**
     * @var string Absolute file path to Autoloader class file.
     */
    protected $autoloaderClassFilePath;
    
    /**
     * @var Autoloader The Autoloader instance instantiated by initializeAutoloader().
     */
    protected $autoloader;
    
    /**
     * @var \Closure The anonymous function used to load files without leaking scope.
     */
    protected $loadFileFunction;
    
    /**
     * @var \Closure The anonymous function used to convert errors into ErrorException.
     */
    protected $errorHandlerFunction;
    
    /**
     * @var DependencyInjectionContainer Object used to wire dependencies.
     */
    protected $dic;
    
    /**
     * @var EventDispatcher Used to initialize and notify objects of various core events.
     */
    protected $events;
    
    /**
     * @var ExceptionHandler Object used to handle exceptions.
     */
    protected $exceptionHandler;
    
    /**
     * @var RouteRegistrations Object used to represent route registrations.
     */
    protected $routeRegistrations;
    
    /**
     * @var Router Object used to route requests to a Callback instance.
     */
    protected $router;
    
    /**
     * @var array $_SERVER
     */
    protected $server;
    
    /**
     * @var array $_GET
     */
    protected $get;
    
    /**
     * @var array $_POST
     */
    protected $post;
    
    /**
     * @var array $_FILES
     */
    protected $files;
    
    /**
     * @var array $_COOKIE
     */
    protected $cookie;
    
    /**
     * @var array $_REQUEST
     */
    protected $request;
    
    /**
     * @var array $_ENV
     */
    protected $env;
    
    /**
     * Constructs the Carrot Sytem object.
     *
     * @param string $configFilePath Absolute file path to DIC configuration file.
     * @param string $routesFilePath Absolute file path to router configuration file.
     * @param string $autoloadFilePath Absolute file path to autoloader configuration file.
     * @param string $autoloaderClassFilePath Absolute file path to Autoloader class file.
     * @param array $server The $_SERVER superglobal.
     * @param array $get The $_GET superglobal.
     * @param array $post The $_POST superglobal.
     * @param array $files The $_FILES superglobal.
     * @param array $cookie The $_COOKIE superglobal.
     * @param array $request The $_REQUEST superglobal.
     * @param array $env The $_ENV superglobal.
     *
     */
    public function __construct($configFilePath, $eventsFilePath, $routesFilePath, $autoloadFilePath, $autoloaderClassFilePath, array $server, array $get, array $post, array $files, array $cookie, array $request, array $env)
    {
        $this->loadFileFunction = function($filePath, array $params)
        {
            extract($params);
            require $filePath;
        };
        
        $this->errorHandlerFunction = function($errno, $errstr, $errfile, $errline)
        {
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        };
        
        $this->eventsFilePath = $eventsFilePath;
        $this->configFilePath = $configFilePath;
        $this->routesFilePath = $routesFilePath;
        $this->autoloadFilePath = $autoloadFilePath;
        $this->autoloaderClassFilePath = $autoloaderClassFilePath;
        $this->server = $server;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookie = $cookie;
        $this->request = $request;
        $this->env = $env;
    }
    
    /**
     * Sets error_reporting to E_ALL | E_STRICT.
     *
     * Carrot advocates strict coding, thus this framework will report
     * all errors, even the tiniest mistakes. To control how errors
     * are going to be displayed, configure exception handling in 
     * Carrot.
     *
     */
    public function reportAllErrors()
    {
        error_reporting(E_ALL | E_STRICT);
    }
    
    /**
     * Checks PHP requirements, exits processing if not fulfilled.
     * 
     * Makes sure magic_quotes_gpc is turned off, register_globals is
     * turned off and makes sure the PHP_VERSION is more than 5.3.0.
     * Will immediately call exit() if any requirement is not
     * fulfilled.
     * 
     */
    public function checkPHPRequirements()
    {
        if (get_magic_quotes_gpc())
        {
            exit('Magic quotes are on. Please turn off magic quotes.');
        }
        
        if (ini_get('register_globals'))
        {
            exit('Register globals are on. Please turn off register globals.');
        }
        
        if (version_compare(PHP_VERSION, '5.3.0') < 0)
        {
            exit('Carrot requires at least PHP 5.3, please upgrade.');
        }
    }
    
    /**
     * Makes sure all the required files exists.
     *
     * Checks to see if DIC configuration file, router configuration
     * file, autoloader configuration file and autoloader class file
     * really exists or not. Exits processing immediately if one of
     * them does not exists.
     *
     */
    public function checkRequiredFileExistence()
    {
        if (!file_exists($this->configFilePath))
        {
            exit("DIC configuration file does not exist ($configFilePath).");
        }
        
        if (!file_exists($this->routesFilePath))
        {
            exit("Router configuration file does not exist ($routesFilePath).");
        }
        
        if (!file_exists($this->autoloadFilePath))
        {
            exit("Autoloader configuration file does not exist ($autoloadFilePath).");
        }
        
        if (!file_exists($this->autoloaderClassFilePath))
        {
            exit("Autoloader class file does not exist ($autoloaderClassFilePath).");
        }
    }
    
    /**
     * Set up the autoloader.
     *
     * Instantiates Autoloader, loads the autoloader configuration
     * file and register the autoloader. The autoloader configuration
     * file is loaded from anonymous function so that it doesn't have
     * any access to this class.
     *
     */
    public function initializeAutoloader()
    {
        require $this->autoloaderClassFilePath;
        $this->autoloader = new Autoloader;
        $loadFile = $this->loadFileFunction;
        $loadFile($this->autoloadFilePath, array('autoloader' => $this->autoloader));
        $this->autoloader->register();
    }
    
    /**
     * Set up the dependency injection container.
     *
     * Instantiates DependencyInjectionContainer (DIC), registers
     * default bindings for Carrot\Core and Carrot\Docs, and loads
     * the user DIC configuration file.
     *
     * You can override default bindings in your configuration file.
     *
     */
    public function initializeDependencyInjectionContainer()
    {
        $this->dic = new DependencyInjectionContainer;
        $this->registerDefaultDICBindings();
        $loadFile = $this->loadFileFunction;
        $loadFile($this->configFilePath, array('dic' => $this->dic));
    }
    
    /**
     * Initialize Events instance.
     * 
     * 
     * 
     */
    public function initializeEventDispatcher()
    {
        $this->events = $this->dic->getInstance(
            new ObjectReference('Carrot\Core\EventDispatcher{Main:Singleton}')
        );
        
        if (file_exists($this->eventsFilePath))
        {
            $loadFile = $this->loadFileFunction;
            $loadFile($this->eventsFilePath, array('events' => $this->events));
        }
        
        $this->events->setDIC($this->dic);
        $this->events->notify('Carrot.Core.System:EventDispatcherReady');
    }
    
    /**
     * Set up the error handler.
     * 
     * Uses set_error_handler to register an anonymous function that
     * converts regular PHP errors to ErrorException instances.
     * 
     */
    public function initializeErrorHandler()
    {
        set_error_handler($this->errorHandlerFunction);
    }
    
    /**
     * Set up the exception handler.
     * 
     * Gets Carrot\Core\ExceptionHandler{Main:Transient} instance from
     * the DIC and sets the exception handler. 
     *
     */
    public function initializeExceptionHandler()
    {
        $this->exceptionHandlerManager = $this->dic->getInstance(
            new ObjectReference('Carrot\Core\ExceptionHandlerManager{Main:Singleton}')
        );
        
        $this->exceptionHandlerManager->setDIC($this->dic);
        $this->exceptionHandlerManager->setDefaultServerProtocol($this->server['SERVER_PROTOCOL']);
        $this->exceptionHandlerManager->set();
    }
    
    /**
     * Set up the router.
     * 
     * Gets Carrot\Core\Router{Main:Singleton} instance from the DIC,
     * loads user's router configuration file, and initializes the
     * router object.
     *
     */
    public function initializeRouter()
    {
        $this->routeRegistrations = new RouteRegistrations(
            $this->dic->getInstance(new ObjectReference('Carrot\Core\Request{Main:Singleton}')),
            $this->dic->getInstance(new ObjectReference('Carrot\Core\AppRequestURI{Main:Singleton}')),
            $this->dic
        );
        
        $loadFile = $this->loadFileFunction;
        $loadFile($this->routesFilePath, array('routes' => $this->routeRegistrations));
        
        // We can only bind this now because RouteRegistrations is not
        // available before, only after now.
        $this->dic->bind('Carrot\Core\Router{Main:Singleton}', array(
            $this->routeRegistrations,
            new ObjectReference('Carrot\Core\AppRequestURI{Main:Singleton}')
        ));
        
        $this->router = $this->dic->getInstance(
            new ObjectReference('Carrot\Core\Router{Main:Singleton}')
        );
        
        $this->events->notify('Carrot.Core.System:routingDone');
    }
    
    /**
     * Routes the request and gets the response from the routine method.
     *
     * After getting the response, this method will immediately send
     * the response to the client.
     *
     */
    public function run()
    {
        $callback = $this->router->doRouting();
        $response = $this->getResponse($callback);
        return $response;
    }
    
    /**
     * Gets the response by running the callback.
     *
     * Will do internal redirection by recursion if the callback
     * returns another instance of Callback. Otherwise will check if
     * return value is an instance of Response or not.
     *
     * Will throw RuntimeException if the callback (essentially your
     * routine method) doesn't return an instance of Response.
     *
     * @throws RuntimeException
     * @param Callback $callback The callback to run.
     * @return Response The response instance from the routine method.
     *
     */
    protected function getResponse(Callback $callback)
    {
        $response = $callback->run($this->dic);
        
        // Do internal redirection
        if ($response instanceof Callback)
        {
            return $this->getResponse($response);
        }
        
        if (!($response instanceof Response))
        {
            $className = $callback->getObjectReference()->getClassName();
            $methodName = $callback->getMethodName();
            throw new RuntimeException("System error in running callback, the routine method {$className}::{$methodName}() doesn't return an instance of Carrot\Core\Response.");
        }
        
        $response->setDefaultServerProtocol($this->server['SERVER_PROTOCOL']);
        return $response;
    }
    
    /**
     * Registers default DIC bindings for core classes.
     *
     * List of registered default DIC bindings for core classes:
     *
     * <code>
     * Carrot\Core\AppRequestURI{Main:Singleton}
     * Carrot\Core\ExceptionHandler{Main:Transient}
     * Carrot\Core\Request{Main:Singleton}
     * </code>
     *
     * Besides that there are also default bindings for Carrot\Docs:
     *
     * <code>
     * Carrot\Docs\Route{Main:Transient}
     * Carrot\Docs\Controller{Main:Transient}
     * Carrot\Docs\View{Main:Transient}
     * </code>
     *
     * You should be able to override the above default bindings in
     * your DIC configuration file (defaults to config.php).
     *
     */
    protected function registerDefaultDICBindings()
    {
        $this->dic->bind('Carrot\Core\AppRequestURI{Main:Singleton}', array(
            new ObjectReference('Carrot\Core\Request{Main:Singleton}')
        ));
        
        $this->dic->bind('Carrot\Core\ExceptionHandlerManager{Main:Singleton}', array(
            array('Exception' => new ObjectReference('Carrot\Core\ExceptionHandler{Main:Transient}'))
        ));
        
        $this->dic->bind('Carrot\Core\Request{Main:Singleton}', array(
            $this->server,
            $this->get,
            $this->post,
            $this->files,
            $this->cookie,
            $this->request,
            $this->env
        ));
        
        // Documentation bindings
        
        $this->dic->bind('Carrot\Docs\Route{Main:Transient}', array(
            new ObjectReference('Carrot\Core\AppRequestURI{Main:Singleton}')
        ));
        
        $this->dic->bind('Carrot\Docs\Controller{Main:Transient}', array(
            new ObjectReference('Carrot\Docs\View{Main:Transient}'),
            new ObjectReference('Carrot\Docs\Model{Main:Singleton}')
        ));
        
        $this->dic->bind('Carrot\Docs\View{Main:Transient}', array(
            new ObjectReference('Carrot\Docs\Model{Main:Singleton}'),
            new ObjectReference('Carrot\Core\Router{Main:Singleton}'),
            'Carrot.Docs.Page'
        ));
    }
}