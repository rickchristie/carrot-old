<?php

namespace Carrot\Framework;

use Carrot\Autoloader\Autoloader,
    Carrot\Injection\Container,
    Carrot\Event\Notifier,
    Carrot\Framework\Routing\Router,
    Carrot\Framework\Error\ExceptionHandlerInterface,
    Carrot\Framework\Initializer\ContainerInitializer,
    Carrot\Framework\Initializer\ExceptionHandlerInitializer,
    Carrot\Framework\Initializer\RouterInitializer,
    Carrot\Framework\Initializer\NotifierInitializer;

/**
 * Represents an application, this class runs the request to
 * response cycle.
 *
 */
class Application
{
    /**
     * Main configuration array.
     * @var Config $config
     */
    private $config;
    
    /**
     * Potentially the autoloader object, so we save it here.
     * This could be either NULL, TRUE, or an instance of the
     * default autoloader class.
     * 
     * @var mixed|Autoloader $autoloader
     */
    private $autoloader;
    
    /**
     * @var ContainerInitializer $containerInitializer
     */
    private $containerInitializer;
    
    /**
     * @var ExceptionHandlerInitializer $exceptionHandlerInitializer
     */
    private $exceptionHandlerInitializer;
    
    /**
     * @var RouterInitializer $routerInitializer
     */
    private $routerInitializer;
    
    /**
     * @var NotifierInitializer $notifierInitializer
     */
    private $notifierInitializer;
    
    /**
     * @var Container $container
     */
    private $container;
    
    /**
     * @var ExceptionHandlerInterface $exceptionHandler
     */
    private $exceptionHandler;
    
    /**
     * @var Router $router
     */
    private $router;
    
    /**
     * @var Notifier $notifier
     */
    private $notifier;
    
    /**
     * Constructor.
     * 
     * If the given autoloader is the default autoloader class, it
     * would be used to get autoloading information for debugging.
     * 
     * @param Config $config
     * @param mixed $autoloader Could be an autoloader object.
     *
     */
    public function __construct(Config $config, $autoloader)
    {
        $this->config = new Config($config);
        $this->autoloader = $autoloader;
        $this->containerInitializer = new ContainerInitializer($config);
        $this->exceptionHandlerInitializer = new ExceptionHandlerInitializer($config);
    }
    
    /**
     * Runs the framework and sends the response.
     * 
     */
    public function run()
    {
        $this->initialize();
        
        if ($this->router->run() == FALSE)
        {
            $this->
        }
    }
    
    /**
     * Initializes all required objects.
     * 
     * @see run()
     *
     */
    private function initialize()
    {
        $this->container = $this->containerInitializer->run();
        $this->exceptionHandler = $this->exceptionHandlerInitializer->run($this->container);
        $this->router = $this->routerInitializer->run($this->container);
        $this->notifier = $this->notifierInitializer->run($this->container);
        $this->exceptionHandlerInitializer->setLogs(
            $this->container,
            $this->router,
            $this->notifier
        );
        
        $this->notifier->notify('Carrot.Framework:initialized');
    }
}