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
 * Assets
 * 
 * Contains paths to assets (CSS files, JS files) used in
 * rendering templates.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Helper;

use InvalidArgumentException;

class Assets
{
    /**
     * @var array Paths to assets in an associative array, relative from the assets root directory given.
     */
    protected $assets;
    
    /**
     * @var string The root directory for assets.
     */
    protected $assetsRootDirectory;
    
    /**
     * Constructor.
     * 
     * Pass the assets array when constructing:
     *
     * <code>
     * $assets = new Assets('http://example.com/assets/', array(
     *     'mainCSS' => 'path/to/style.css',
     *     'jQuery' => 'path/to/jquery.js',
     *     'logo' => 'path/to/logo.png'
     * ));
     * </code>
     * 
     * @param string $assetsRootDirectory The root directory for assets.
     * @param array $assets Relative paths to assets in an associative array.
     * 
     */
    public function __construct($assetsRootDirectory, array $assets)
    {
        $this->assetsRootDirectory = $assetsRootDirectory;
        $this->assets = array();
        
        foreach ($assets as $name => $path)
        {
            $this->assets[$name] = htmlentities($this->assetsRootDirectory . $path, ENT_QUOTES);
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
     * Get the assets directory.
     *
     * @return string The root directory for assets.
     *
     */
    public function getAssetsDir()
    {
        return $this->assetsRootDirectory;
    }
}