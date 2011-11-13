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
 * Navigational item.
 *
 * Represents a navigational item in the docs, can be either a
 * section or a documentation page. Contains title, type, real
 * path and the routing arguments needed to generate the URI to
 * it.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Docs;

class NavigationItem
{
    /**
     * @var string The title of this navigation item.
     */
    protected $title;
    
    /**
     * @var string The type of this navigation item.
     */
    protected $type;
    
    /**
     * @var array List of allowed strings to be used as {@see $type}.
     */
    protected $allowedTypes = array(
        'section',
        'doc'
    );
    
    /**
     * @var array Routing arguments needed to generate the URI to
     *      this particular navigation item.
     */
    protected $routingArgs;
    
    /**
     * @var string Real path to the physical counterpart of this
     *      item, either a directory or a file, depending on the
     *      type, without trailing directory separator.
     */
    protected $realPath;
    
    /**
     * @var bool If TRUE, then this navigation item is the one
     *      currently being displayed. FALSE otherwise.
     */
    protected $current = FALSE;
    
    /**
     * Constructor.
     * 
     * The routing arguments array is used to run
     * {@see HTTPRoute::getURI()}, and thus contains hierarchical
     * path to this navigational item. For example, in a directory
     * structure like this:
     *
     * <code>
     * - 1. Introduction
     *     - 1. What is Carrot?.html
     *     - 2. Calculator Tutorial
     *         - 1. Creating Your First Action
     * - 2. Autoloading
     * </code>
     * 
     * For the title '1. Creating Your First Action', the routing
     * arguments would be:
     *
     * <code>
     * $routingArgs = array(
     *     '1-Introduction',
     *     '2-Calculator-Tutorial',
     *     '1-Creating-Your-First-Action'
     * );
     * </code>
     * 
     * The real path would be something like:
     *
     * <code>
     * '/root/1. Introduction/2. Calculator Tutorial/1. Creating Your First Action'
     * </code>
     * 
     * The type would be 'doc', since it is not a directory, thus
     * not a 'section'.
     * 
     * @param string $title The title of this navigation item.
     * @param string $type The type of this navigation item.
     * @param array $routingArgs Routing arguments needed to generate
     *        the URI to this particular navigation item.
     * @param string $realPath Real path to the physical counterpart
     *        of this item, either a directory or a file, depending
     *        on the type, without trailing directory separator.
     *
     */
    public function __construct($title, $type, array $routingArgs, $realPath)
    {
        $this->title = $title;
        $this->type = $type;
        $this->routingArgs = $routingArgs;
        $this->realPath = $realPath;
    }
    
    /**
     * Get the title of this navigation item.
     * 
     * @return string
     *
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Check if this navigation item is a section or not.
     *
     * @return bool TRUE if this is a section, FALSE otherwise.
     *
     */
    public function isSection()
    {
        return $this->type == 'section';
    }
    
    /**
     * Check if this navigation item is a documentation page or not.
     *
     * @return bool TRUE if this is a documentation page, FALSE
     *         otherwise.
     *
     */
    public function isDoc()
    {
        return $this->type == 'doc';
    }
    
    /**
     * Get the routing argument array for generating the URI.
     *
     * @return array
     *
     */
    public function getRoutingArgs()
    {
        return $this->routingArgs;
    }
    
    /**
     * Get the item ID for this navigation item.
     *
     * Please note that navigation item IDs are only unique to the
     * section they belong to. This means you can have a path
     * hierarchy like this:
     *
     * <code>
     * '1-introduction' -> level 0
     * '1-calculator-tutorial' -> level 1
     * '1-introduction' -> level 2
     * </code>
     *
     * This method returns the last item ID from the routing
     * arguments array.
     *
     * @return string
     *
     */
    public function getItemID()
    {
        return end($this->routingArgs);
    }
    
    /**
     * Get the real path to the physical counterpart of this
     * navigation item, without trailing slash.
     *
     * @return string
     *
     */
    public function getRealPath()
    {
        return $this->realPath;
    }
    
    /**
     * Mark this navigation item as the one currently being
     * displayed in the request.
     *
     * @see $current
     *
     */
    public function markAsCurrent()
    {
        $this->current = TRUE;
    }
    
    /**
     * Check if this navigation item is the one currently being
     * displayed in the request.
     *
     * @return bool
     *
     */
    public function isCurrent()
    {
        return $this->current;
    }
    
    /**
     * Get the relative URI of this navigation item using the given
     * {@see RouterInterface} object.
     *
     * @param RouterInterface $router Used to generate the URI.
     * @param string $routeID The ID of the route.
     * @return string
     *
     */
    public function getRelativeURI(RouterInterface $router, $routeID)
    {
        return $router->getURI($routeID, $this->routingArgs, FALSE);
    }
}