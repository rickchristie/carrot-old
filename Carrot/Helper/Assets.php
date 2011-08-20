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
// ---------------------------------------------------------------
 * 
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
     * @var array Relative paths to assets in an associative array.
     */
    protected $assets;
    
    /**
     * @var string Base path
     */
    protected $basePath;
    
    protected $baseURL;
    
    /**
     * Constructor.
     * 
     * asdf
     * 
     * @param AppRequestURI $appRequestURI 
     * @param array $assets Relative paths to assets in an associative array.
     * 
     */
    public function __construct(AppRequestURI $appRequestURI, array $assets)
    {
        $this->basePath = $appRequestURI->getBasePath();
        $this->baseURL = $appRequestURI->getBaseURL();
        $this->assets = array();
        
        foreach ($assets as $name => $path)
        {
            $path = ltrim($path, '/');
            $this->assets[$name]['relative'] = $this->basePath . $path;
            $this->assets[$name]['absolute'] = $this->baseURL . $path;
        }
    }
    
    /**
     * asf
     *
     */
    public function get($assetName, $absoluteURL = FALSE)
    {
        if (!array_key_exists($assetName, $this->assets))
        {
            throw new InvalidArgumentException("Asset error in getting path. Asset name '{$assetName}' is not defined.");
        }
        
        if ($absoluteURL)
        {
            return $this->assets[$assetName]['absolute'];
        }
        else
        {
            return $this->assets[$assetName]['relative'];
        }
    }
    
    public function getBasePath()
    {
        return $this->basePath;
    }
    
    public function getBaseURL()
    {
        return $this->baseURL;
    }
}