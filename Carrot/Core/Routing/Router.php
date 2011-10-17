<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Carrot's router.
 *
 * Routing is the process of translating a user's request into an
 * instance of {@see Destination}. It is done by looping through
 * registered route objects and telling them to route the request
 * until one of them returns an instance of Destination.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Routing;

use RuntimeException,
    InvalidArgumentException,
    Carrot\Core\Request\RequestInterface,
    Carrot\Core\DependencyInjection\Container,
    Carrot\Core\Routing\Config\ConfigInterface,
    Carrot\Core\Routing\Route\HTTPRouteInterface,
    Carrot\Core\Routing\Route\CLIRouteInterface;

class Router implements RouterInterface
{
    /**
     * @var ConfigInterface Routing configuration object.
     */
    protected $config;
    
    /**
     * @var RequestInterface The request object.
     */
    protected $request;
    
    /**
     * @var Container Dependency injection container.
     */
    protected $container;
    
    /**
     * @var array The configuration array that contains the base HTTP
     *      URI of this application.
     */
    protected $baseHTTPURIConfig;
    
    /**
     * @var string Fully qualified class name of the HTTPURIInterface
     *      implementation to be used in this application.
     */
    protected $HTTPURIClass;
    
    /**
     * @var HTTPURIInterface The base URI of the application, used as
     *      a helper object for HTTP routes to route request and
     *      build their URI.
     */
    protected $baseHTTPURI;
    
    /**
     * @var HTTPURIInterface The request URI of the current request,
     *      used as a helper object to help HTTP routes route the
     *      current request.
     */
    protected $requestHTTPURI;
    
    /**
     * @var array List route IDs retrieved from
     *      {@see ConfigInterface::getRouteIDs()}.
     */
    protected $routeIDs = array();
    
    /**
     * @var array List of instantiated route objects.
     */
    protected $routeObjects = array();
    
    /**
     * @var string|NULL The currently active route ID, or NULL if
     *      there is no active route (failure in routing).
     */
    protected $activeRouteID;
    
    /**
     * @var bool TRUE if routing has been completed, FALSE otherwise.
     *      This helps making sure that the routing process is only
     *      run once.
     */
    protected $routingCompleted = FALSE;
    
    /**
     * Constructor.
     * 
     * Generates base HTTP URI and HTTP request URI at construction.
     * Assumes that the base HTTP URI configuration array given is
     * already checked for validity.
     * 
     * @param ConfigInterface $config Routing configuration object.
     * @param RequestInterface $request The request object.
     * @param Container $container Dependency injection container.
     * @param array $baseHTTPURIConfig The configuration array that
     *        contains the base HTTP URI of this application.
     * @param string $HTTPURIClass Fully qualified class name of the
     *        HTTPURIInterface implementation to be used in this
     *        application.
     * 
     */
    public function __construct(ConfigInterface $config, RequestInterface $request, Container $container, array $baseHTTPURIConfig, $HTTPURIClass)
    {
        $this->config = $config;
        $this->request = $request;
        $this->container = $container;
        $this->routeIDs = $config->getRouteIDs();
        $this->baseHTTPURIConfig = $baseHTTPURIConfig;
        $this->HTTPURIClass = $HTTPURIClass;
    }
    
    /**
     * Routes the request, using information gathered from the
     * routing configuration object.
     * 
     * If the request is CLI, checked using
     * {@see RequestInterface::isCLI()}, this method will ignore HTTP
     * routes and vice versa.
     * 
     * @return Destination
     *
     */
    public function routeRequest()
    {
        if ($this->routingCompleted)
        {
            throw new RuntimeException('Router error. Request already routed, you must not call Carrot\Core\Routing\Router::routeRequest() more than once.');
        }
        
        if ($this->request->isCLI())
        {
            return $this->routeCLIRequest();
        }
        
        $this->baseHTTPURI = $this->generateBaseHTTPURI();
        $this->requestHTTPURI = $this->generateRequestHTTPURI();
        return $this->routeHTTPRequest();
    }
    
    /**
     * Generate a HTTP URI string from the given HTTP route ID and
     * arguments.
     *
     * This is the reverse routing method, useful in templates and
     * views where you wanted to generate URIs.
     *
     * @throws InvalidArgumentException If the route ID does not
     *         exist, or if the route is a CLI route.
     * @param string $routeID The ID of the route to generate the
     *        URI we wanted.
     * @param array $args Associative array, contains argument names
     *        and their values.
     * @param bool $absolute If TRUE, this method should return an
     *        absolute URI string, otherwise this method returns a
     *        relative URI string.
     * @return string
     *
     */
    public function getURI($routeID, array $args = array(), $absolute = FALSE)
    {
        if (!isset($this->routeIDs[$routeID]))
        {
            throw new InvalidArgumentException("Router error in trying to get URI string. The route '{$routeID}' does not exist.");
        }
        
        if ($this->routeIDs[$routeID] != 'HTTP')
        {
            throw new InvalidArgumentException("Router error in trying to get URI string. The route '{$routeID}' is not a HTTP route.");
        }
        
        $route = $this->getRouteObject($routeID);
        $baseHTTPURI = clone $this->baseHTTPURI;
        return $route->getURI($args, $baseHTTPURI, $absolute);
    }
    
    /**
     * Gets the list of route IDs, returns the same variable as
     * {@see Config\ConfigInterface::getRouteIDs()}.
     *
     * @return array
     *
     */
    public function getRouteIDs()
    {
        return $this->routeIDs;
    }
    
    /**
     * Gets the currently active route ID.
     *
     * @return string|NULL Currently active route ID, NULL if the
     *         routing process is not completed yet, FALSE if there
     *         is an error in routing.
     *
     */
    public function getActiveRouteID()
    {
        return $this->activeRouteID;
    }
    
    /**
     * Generates a HTTPURIInterface instance that contains the base
     * HTTP URI for this application.
     * 
     * Will guess the base HTTP URI information if the configuration
     * contains empty string.
     * 
     * @return HTTPURIInterface The base HTTP URI.
     *
     */
    protected function generateBaseHTTPURI()
    {
        $scheme = $this->baseHTTPURIConfig['scheme'];
        $authority = $this->baseHTTPURIConfig['authority'];
        $path = $this->baseHTTPURIConfig['path'];
        
        if (empty($scheme))
        {
            $scheme = $this->guessBaseHTTPURIScheme();
        }
        
        if (empty($authority))
        {
            $authority = $this->guessBaseHTTPURIAuthority();
        }
        
        if (empty($path))
        {
            $path = $this->guessBaseHTTPURIPath();
        }
        
        $HTTPURIClass = $this->HTTPURIClass;
        return new $HTTPURIClass(
            $scheme,
            $authority,
            $path
        );
    }
    
    /**
     * Guess the 'scheme' part of the base URI using $_SERVER
     * variables, returns either 'http' or 'https'.
     *
     * Uses RequestInterface::isHTTPS() to check if the request is
     * HTTPS, otherwise assumes that the request is HTTP.
     *
     * @return string Either 'https' or 'http'.
     *
     */
    protected function guessBaseHTTPURIScheme()
    {
        if ($this->request->isHTTPS())
        {
            return 'https';
        }
        
        return 'http';
    }
    
    /**
     * Guess the 'authority' part of the base URI using $_SERVER
     * variables, the SERVER_NAME variable in particular.
     * 
     * Returns an empty string if the SERVER_NAME variable doesn't
     * exist.
     * 
     * @return string
     *
     */
    protected function guessBaseHTTPURIAuthority()
    {
        $serverName = $this->request->getServer('SERVER_NAME', '');
        
        if (empty($serverName))
        {
            throw new RuntimeException('Router error in generating base HTTP URI. The $_SERVER[\'SERVER_NAME\'] variable cannot be used to guess the authority part of the HTTP URI.');
        }
        
        return $serverName;
    }
    
    /**
     * Guess the 'path' part of the base URI using $_SERVER
     * variables, particularly the SCRIPT_NAME variable.
     * 
     * Assuming that each request will be handled by the 'index.php'
     * file, we use SCRIPT_NAME to get the path from the root to the
     * request handler. SCRIPT_NAME is used because it is available
     * to both Apache and IIS and are consistent in form (using
     * slashes as separator):
     * 
     * <code>
     * /index.php -> /
     * /carrot-dev/index.php -> /carrot-dev/
     * </code>
     * 
     * Base path returned will always have a trailing slash.
     * 
     * @return string
     *
     */
    protected function guessBaseHTTPURIPath()
    {
        $path = str_ireplace('/index.php', '', $this->request->getServer('SCRIPT_NAME', ''));
        
        if (empty($path) or substr($path, -1) != '/')
        {
            $path .= '/';
        }
        
        return $path;
    }
    
    /**
     * Generates request HTTP URI based on information provided by
     * the request object.
     *
     * @return HTTPURIInterface The request URI.
     *
     */
    protected function generateRequestHTTPURI()
    {
        if ($this->request->isHTTPS())
        {
            $scheme = 'https';
        }
        else
        {
            $scheme = 'http';
        }
        
        $serverName = $this->request->getServer('SERVER_NAME', '');
        $requestURI = $this->request->getServer('REQUEST_URI', '');
        
        if (empty($serverName) OR empty($requestURI))
        {
            throw new RuntimeException('Router error in generating request HTTP URI. Either \$_SERVER[\'SERVER_NAME\'] or $_SERVER[\'REQUEST_URI\'] cannot be used.');
        }
        
        $HTTPURIClass = $this->HTTPURIClass;
        return new $HTTPURIClass(
            $scheme,
            $serverName,
            $requestURI
        );
    }
    
    /**
     * Loop through the CLI routes until one of them returns an
     * instance of Destination.
     * 
     * @see getNoMatchingCLIRouteDestination()
     * @return Destination
     *
     */
    protected function routeCLIRequest()
    {
        foreach ($this->routeIDs as $routeID => $type)
        {
            if ($type != 'CLI')
            {
                continue;
            }
            
            $route = $this->getRouteObject($routeID);
            
            if ($route instanceof CLIRouteInterface == FALSE)
            {
                throw new RuntimeException("Router error in routing CLI request. The CLI route '{$routeID}' does not implement Carrot\Core\Routing\Route\CLIRouteInterface.");
            }
            
            $destination = $route->route();
            
            if ($destination instanceof Destination)
            {
                return $destination;
            }
        }
        
        return $this->getNoMatchingCLIRouteDestination();
    }
    
    /**
    //---------------------------------------------------------------
     * Loop through the HTTP routes until one of them returns an
     * instance of Destination.
     * 
     * 
     *
     */
    protected function routeHTTPRequest()
    {   
        foreach ($this->routeIDs as $routeID => $type)
        {
            if ($type != 'HTTP')
            {
                continue;
            }
            
            $route = $this->getRouteObject($routeID);
            
            if ($route instanceof HTTPRouteInterface == FALSE)
            {
                throw new RuntimeException("Router error in routing HTTP request. The HTTP route '{$routeID}' does not implement Carrot\Core\Routing\Route\HTTPRouteInterface.");
            }
            
            $baseHTTPURI = clone $this->baseHTTPURI;
            $requestHTTPURI = clone $this->requestHTTPURI;
            $destination = $route->route($requestHTTPURI, $baseHTTPURI);
            
            if ($destination instanceof Destination)
            {
                return $destination;
            }
        }
        
        return $this->getNoMatchingHTTPRouteDestination();
    }
    
    /**
     * Get route object from the given ID.
     * 
     * Returns from the route objects cache when it can, otherwise
     * uses {@see ConfigInterface::getRoute()}.
     * 
     * @param string $routeID The ID of the route whose instance we
     *        wanted to get.
     * @return HTTPRouteInterface|CLIRouteInterface
     *
     */
    protected function getRouteObject($routeID)
    {
        if (isset($this->routeObjects[$routeID]))
        {
            return $this->routeObjects[$routeID];
        }
        
        $route = $this->config->getRoute($routeID, $this->container);
        $this->routeObjects[$routeID] = $route;
        return $route;
    }
    
    /**
     * Get the Destination to return when there is no matching route
     * for a CLI request.
     *
    //---------------------------------------------------------------
     * Will first ask 
     * 
     * @return Destination
     *
     */
    public function getNoMatchingCLIRouteDestination()
    {
        
    }
    
    /**
     * Get the Destination to return when there is no matching route
     * for a HTTP request.
     * 
     * @return Destination
     *
     */
    protected function getNoMatchingHTTPRouteDestination()
    {
        
    }
}