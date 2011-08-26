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
 * Assets
 * 
 * Contains paths to assets (CSS files, JS files) used in
 * rendering templates. Resolves the paths given relative to the
 * base path or base URL from AppRequestURI.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Helper;

use RuntimeException;
use InvalidArgumentException;
use Carrot\Core\AppRequestURI;

class Assets
{
    /**
     * @var array Paths to assets in an associative array, relative from the base URL or the base path.
     */
    protected $assets;
    
    /**
     * @var string Base path to the assets in an associative array.
     */
    protected $basePath;
    
    /**
     * Constructor.
     * 
     * Pass the assets array when constructing:
     *
     * <code>
     * $assets = new Assets($appRequestURI, array(
     *     'mainCSS' => 'path/to/style.css',
     *     'jQuery' => 'path/to/jquery.js',
     *     'logo' => 'path/to/logo.png'
     * ));
     * </code>
     * 
     * @param AppRequestURI $appRequestURI Needed to get the base path/URL.
     * @param array $assets Relative paths to assets in an associative array, without slash prefix.
     * 
     */
    public function __construct(AppRequestURI $appRequestURI, array $assets)
    {
        // TODO: Once DIC is refactored, assets must accept base path/URL instead of AppRequestURI.
        $this->basePath = $appRequestURI->getBasePath();
        $this->assets = array();
        
        foreach ($assets as $name => $path)
        {
            $path = ltrim($path, '/');
            $this->assets[$name] = htmlentities($this->basePath . $path, ENT_QUOTES);
        }
    }
    
    /**
     * Get the path of the asset of the given name.
     *
     * Access the relative paths from your templates: 
     *
     * <code>
     * <link rel="stylesheet" media="screen" href="<?php echo $assets->get('mainCSS') ?>" type="text/css" />
     * </code>
     *
     * The returned path is already cleaned with htmlentities() and
     * safe to be printed.
     * 
     * @param string $assetName The name of the asset, as defined in construction.
     * 
     */
    public function get($assetName)
    {
        if (!array_key_exists($assetName, $this->assets))
        {
            throw new InvalidArgumentException("Asset error in getting path. Asset name '{$assetName}' is not defined.");
        }
        
        return $this->assets[$assetName];
    }
    
    /**
     * Get base path, with trailing slash.
     * 
     * @return string
     * 
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
    
    /**
     * Get base URL, with trailing slash.
     * 
     * @return string
     * 
     */
    public function getBaseURL()
    {
        return $this->baseURL;
    }
}