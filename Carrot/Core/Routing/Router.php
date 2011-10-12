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
 * Routing is the process of tranlating a user's request into an
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
    Carrot\Core\Request\RequestInterface,
    Carrot\Core\DependencyInjection\Container,
    Carrot\Core\Routing\Config\ConfigInterface;

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
     * @var URL 
     */
    protected $baseURL;
    
    /**
     * @var array List route IDs retrieved from
     *      {@see ConfigInterface::getRouteIDs()}.
     */
    protected $routeIDs = array();
    
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
    //---------------------------------------------------------------
     * Generates base URI
     * 
     * Assumes that the base URI configuration array has been
     * validated by \Carrot\Core\Application before being injected.
     * 
     * @param ConfigInterface $config Routing configuration object.
     * @param RequestInterface $request The request object.
     * @param Container $container Dependency injection container.
     * @param string $baseURL The base URL of this application, 
     * 
     */
    public function __construct(ConfigInterface $config, RequestInterface $request, Container $container, $baseURL)
    {
        $this->config = $config;
        $this->request = $request;
        $this->container = $container;
        $this->baseURI = $this->generateBaseURI($baseURIConfig);
        $this->requestURI = $this->generateRequestURI();
        $this->routeIDs = $config->getRouteIDs();
    }
    
    /**
    //---------------------------------------------------------------
     * Routes the request, using information gathered from the
     * routing configuration object.
     * 
     * 
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
        
        
    }
    
    /**
     * Generate URI string from the given HTTP route ID and
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
     * @param bool $absolute If TRUE, will ask the route to generate
     *        absolute URI, relative URI otherwise.
     *
     */
    public function getURI($routeID, array $args = array(), $absolute = FALSE)
    {
        
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
        return $this->activeRouteIDs;
    }
    
    /**
     * Generates an URI instance that contains the base URI.
     * 
     * Will guess the base URI information if the given configuration
     * contains empty strings.
     * 
     * @return URI The base URI.
     *
     */
    protected function generateBaseURI(array $baseURIConfig)
    {
        if (empty($baseURIConfig['scheme']))
        {
            $baseURIConfig['scheme'] = $this->guessBaseURIScheme();
        }
        
        if (empty($baseURIConfig['authority']))
        {
            $baseURIConfig['authority'] = $this->guessBaseURIAuthority();
        }
        
        if (empty($baseURIConfig['path']))
        {
            $baseURIConfig['path'] = $this->guessBaseURIPath();
        }
        
        if (empty($baseURIConfig['query']))
        {
            $baseURIConfig['query'] = '';
        }
        
        return new URI(
            $baseURIConfig['scheme'],
            $baseURIConfig['authority'],
            $baseURIConfig['path'],
            $baseURIConfig['query']
        );
    }
    
    /**
     * Guess the 'scheme' part of the base URI using $_SERVER
     * variables, returns either 'http' or 'https'.
     *
     * If this method somehow fails to guess (the request might be
     * CLI, which means variables needed to do the guessing does not
     * exist), it will return an empty string instead.
     *
     * @return string
     *
     */
    protected function guessBaseURIScheme()
    {
        if ($this->request->isCLI())
        {
            return '';
        }
        
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
    protected function guessBaseURIAuthority()
    {
        return $this->request->getServer('SERVER_NAME', '');
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
    protected function getBaseURIPath()
    {
        $path = str_ireplace('/index.php', '', $this->getServer('SCRIPT_NAME', ''));
        
        if (empty($path) or substr($path, -1) != '/')
        {
            $path .= '/';
        }
        
        return $path;
    }
    
    /**
     * Generates request URI based on information provided by the
     * request object.
     *
     * @return URI The request URI.
     *
     */
    protected function generateRequestURI()
    {
        $requestURI = $this->request->getServer('REQUEST_URI', '');
        $parsed = parse_url($requestURI);
        
        if (!isset($parsed['path']))
        {
            $parsed['path'] = '';
        }
        
        if (!isset($parsed['query']))
        {
            $parsed['query'] = '';
        }
        
        if (!isset($parsed['query']))
        {
            
        }
    }
}