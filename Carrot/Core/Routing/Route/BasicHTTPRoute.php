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
 * Basic HTTP route.
 *
//---------------------------------------------------------------
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Routing\Route;

use Carrot\Core\Request\RequestInterface,
    Carrot\Core\Routing\URI;

class BasicHTTPRoute implements RouteInterface
{
    protected $id;
    
    /**
     * Constructor.
     * 
     * 
     *
     */
    public function __construct(array $config, RequestInterface $request, URI $baseURI)
    {
        
    }
}