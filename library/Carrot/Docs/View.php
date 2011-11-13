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
 * Documentation view.
 * 
 * The presentation layer of the documentation package. Queries
 * the {@see Storage} for {@see Page} instances and renders them
 * to HTML. Although not mentioned in the code, this class
 * tightly couples itself with the behavior of {@see HTTPRoute}.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Docs;

use Carrot\Routing\RouterInterface,
    Carrot\Response\HTTPResponse;

class View
{
    /**
     * @var Storage Represents the documents storage.
     */
    protected $storage;
    
    /**
     * @var RouterInterface The router, used for two way routing
     *      purposes.
     */
    protected $router;
    
    /**
     * @var string The route ID used when adding {@see HTTPRoute} to
     *      the route configuration object.
     */
    protected $routeID;
    
    /**
     * @var string The directory containing assets like css,
     *      javascript, and the template.
     */
    protected $assetsDirectory;
    
    /**
     * @var array List of allowed extensions for static assets and
     *      their respective mime-type.
     */
    protected $mimeTypes = array(
        'css' => 'text/css',
        'js' => 'application/x-javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif'
    );
    
    /**
     * Constructor.
     * 
     * @param Storage $storage Represents the documents storage.
     * @param RouterInterface $router The router, used for two way
     *        routing purposes.
     * @param string $routeID The route ID when adding
     *        {@see HTTPRoute} to the route configuration object.
     * @param string $assetsDirectory The path to the directory that
     *        contains the assets, {@see setAssetsDirectory()}.
     *
     */
    public function __construct(Storage $storage, RouterInterface $router, $routeID, $assetsDirectory = NULL)
    {
        $this->storage = $storage;
        $this->router = $router;
        $this->routeID = $routeID;
        $this->setAssetsDirectory($assetsDirectory);
    }
    
    /**
     * Set assets directory.
     *
     * The assets directory must contain 'template.php' as the master
     * template file and the folder 'Static' which contains static
     * assets like .js and .css files. The template file will have
     * access to this object via '$this'.
     * 
     * You can use the shortcut method {@see getURI()} and
     * {@see getStaticAssetURI()} to bypass calling the router directly in
     * your template file.
     * 
     * @param string $assetsDirectory The path to the directory that
     *        contains the assets used for rendering docs pages.
     *
     */
    public function setAssetsDirectory($assetsDirectory)
    {
        if ($assetsDirectory == NULL)
        {
            $assetsDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'Assets';
        }
        
        $assetsDirectoryAbsolute = realpath($assetsDirectory);
        
        if ($assetsDirectoryAbsolute == FALSE OR is_dir($assetsDirectoryAbsolute) == FALSE)
        {
            throw new InvalidArgumentException();
        }
        
        $this->assetsDirectory = $assetsDirectoryAbsolute;
    }
    
    /**
     * Get the URI to the given documentation page, encoded for HTML
     * output.
     * 
     * @param array $pagePathArray Hierarchical path to the
     *        documentation page/section to get.
     * @param bool $absolute If TRUE, will return absolute URL.
     *        Otherwise returns relative URL.
     *
     */
    public function getURI(array $routingArgs = array(), $absolute = FALSE)
    {
        $uri = $this->router->getURI(
            $this->routeID,
            $routingArgs,
            $absolute
        );
        
        return htmlspecialchars($uri, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Get the URI to the given static asset name via the router,
     * encoded for HTML output.
     * 
     * @param string $assetName The name of the static asset whose
     *        URI is to be returned.
     * @param bool $absolute If TRUE, will return absolute URL.
     *        Otherwise returns relative URL.
     *
     */
    public function getStaticAssetURI($assetName, $absolute = FALSE)
    {
        $routingArgs = array('assets', $assetName);
        $uri = $this->router->getURI(
            $this->routeID,
            $routingArgs,
            $absolute
        );
        
        return htmlspecialchars($uri, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Get a documentation page from the given hierarchical path
     * array.
     * 
     * The hierarchical path array will be sent directly to
     * {@see Storage::getPage()}.
     * 
     * @param array $routingArgs The routing arguments given by
     *        {@see HTTPRoute}.
     * @return HTTPResponse If the documentation page isn't found,
     *         a 404 response will be returned instead.
     *
     */
    public function getDocumentation(array $routingArgs)
    {
        $page = $this->storage->getPage($routingArgs);
        
        if ($page instanceof Page == FALSE)
        {
            return $this->get404Page();
        }
        
        $body = $this->loadTemplate($page);
        return new HTTPResponse($body);
    }
    
    /**
     * Get the static asset requested by the given routing argument.
     * 
     * Only .css, .js, .jpg, and .png files are allowed. Others are
     * ignored by returning an empty 404 file not found response.
     * Static assets will be loaded from 'Static' folder inside the
     * assets directory.
     * 
     * @param string $assetName File name of the static asset that
     *        is requested.
     * @return HTTPResponse Response object containing the static
     *         asset or a 404 header.
     *
     */
    public function getStaticAsset($assetName)
    {
        if (preg_match('/^[^\\.]+\\.(css|js|gif|jpg|png)$/uD', $assetName, $matches) == FALSE)
        {
            return $this->get404Page();
        }
        
        $extension = $matches[1];
        $mimeType = $this->mimeTypes[$extension];
        $filePath = $this->assetsDirectory . DIRECTORY_SEPARATOR
                  . 'Static' . DIRECTORY_SEPARATOR
                  . $assetName;
        
        if (file_exists($filePath) == FALSE)
        {
            return $this->get404Page();
        }
        
        $content = file_get_contents($filePath);
        $size = filesize($filePath);
        $response = new HTTPResponse($content);
        $response->addHeader('Content-Type', $mimeType);
        $response->addHeader('Content-Length', $size);
        return $response;
    }
    
    /**
     * Get the default 404 template string in a {@see HTTPResponse}
     * instance.
     * 
     * @see getDoc()
     * @return HTTPResponse
     *
     */
    public function get404Page()
    {
        $page = $this->storage->getIndexPage();
        $page->setTitle('Page Not Found!');
        $page->setContent(
            '<h1>Page Not Found!</h1>
            <p>
                We are sorry for the inconvenience, but the documentation
                page you requested was not found. Feel free to search browse
                the documentation from the navigation bar on the left.
            </p>
            <pre>Oh holy star
The prophets beckoned
Page is not found</pre>'
        );
        
        $body = $this->loadTemplate($page);
        return new HTTPResponse($body, 404);
    }
    
    /**
     * Check if the routing arguments are actually requestin for an
     * asset or not.
     *
     * @return bool TRUE if it's a request for asset, FALSE
     *         otherwise.
     *
     */
    protected function isRequestingForAsset(array $routingArgs)
    {
        return (
            isset($routingArgs[0], $routingArgs[1]) AND
            $routingArgs[0] == 'assets' AND
            $routingArgs[1] != 'template.php'
        );
    }
    
    /**
     * Loads the 'template.php' file inside the assets directory,
     * uses output buffering to return output as string.
     * 
     * @param Page $page The page to render.
     * @return string
     *
     */
    protected function loadTemplate(Page $page)
    {   
        ob_start();
        require $this->assetsDirectory . DIRECTORY_SEPARATOR . 'template.php';
        return ob_get_clean();
    }
}