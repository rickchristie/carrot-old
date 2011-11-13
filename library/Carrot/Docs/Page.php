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
 * Documentation page.
 *
 * Represents a documentation page. Contains the page title, the
 * content, and the navigation to display in array form.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Docs;

use InvalidArgumentException,
    Carrot\Routing\RouterInterface;

class Page
{
    /**
     * @var string The title of this page.
     */
    protected $title;
    
    /**
     * @var string The content of this page.
     */
    protected $content;
    
    /**
     * @var array The parent sections of this page, sorted from the
     *      deepest parent.
     */
    protected $parentSections;
    
    /**
     * @var array List of navigation items to display.
     */
    protected $navigation;
    
    /**
     * Constructor.
     * 
     * @param string $title The title of this page.
     * @param string $content The content of this page.
     * @param array $parentSections The parent sections of this page,
     *        sorted from the deepest parent.
     * @param array $navigation List of navigation items to display
     * 
     */
    public function __construct($title, $content, array $parentSections, array $navigation)
    {
        $this->title = $title;
        $this->content = $content;
        $this->parentSections = $parentSections;
        $this->navigation = $navigation;
    }
    
    /**
     * Set the title for this page.
     *
     * @param string $title The title for this page, unescaped for
     *        HTML output.
     *
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Get the page title, escaped for HTML output.
     * 
     * You can pass title to print if it's empty, along with prefix
     * and suffix to display. They will be concatenated before
     * being escaped. The prefix and suffix would not be displayed
     * if the title is empty, the $titleIfEmpty argument will be
     * displayed instead on that occasion.
     *
     * @return string
     *
     */
    public function getTitle($titleIfEmpty = '', $prefix = '', $suffix = '')
    {
        if (empty($this->title))
        {
            return htmlspecialchars($titleIfEmpty, ENT_QUOTES, 'UTF-8');
        }
        
        $title = $prefix . $this->title . $suffix;
        return htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Set content for this page.
     *
     * @param string $content The content for this page, unescaped
     *        for HTML output.
     *
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
    
    /**
     * Get the page's content.
     *
     * @return string
     *
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Renders parent sections of this page into HTML list tags.
     * 
     * Example of a rendering:
     * 
     * <code>
     * <li>
     *     <a href="http://example.com/guides/1-Introduction/">
     *         Introduction
     *     </a>
     * </li>
     * ...
     * </code>
     *
     * @param RouterInterface $router Used to get the URI to each
     *        section.
     * @param string $routeID The ID of the route that routes to the
     *        documentation package.
     * @return string Section parents rendered to HTML lists and
     *         escaped for HTML output.
     *
     */
    public function renderParentSectionsToList(RouterInterface $router, $routeID)
    {
        $string = '';
        
        foreach ($this->parentSections as $navItem)
        {   
            $title = $navItem->getTitle();
            $uri = $router->getURI(
                $routeID,
                $navItem->getRoutingArgs()
            );
            
            $string .= "
                <li>
                    <a href=\"{$uri}\">{$title}</a>
                </li>
            ";
        }
        
        return $string;
    }
    
    /**
     * Renders navigation into HTML list tags.
     * 
     * Example of a rendering:
     * 
     * <code>
     * <li class="section">
     *     <a href="http://example.com/guides/1-Introduction/">
     *         Introduction
     *     </a>
     * </li>
     * ...
     * </code>
     * 
     * The <li> tag will have either 'section' or 'doc' class based
     * the navigation item's type. The <a> tag will have 'current'
     * class if the navigation item is the one currently active.
     * 
     * The class 'current' will be added to the <a> tag if the
     * navigation item is the currently displayed page.
     * 
     * @param RouterInterface $router Used to get the URI to each
     *        navigation item.
     * @param string $routeID The ID of the route that routes to the
     *        documentation package.
     * @return string Navigation for the current section rendered to
     *         HTML lists and escaped for HTML output.
     *
     */
    public function renderNavigationToList(RouterInterface $router, $routeID)
    {
        $string = '';
        
        foreach ($this->navigation as $navItem)
        {
            $anchorClass = '';
            $listClass = 'doc';
            $title = $navItem->getTitle();
            $uri = $router->getURI(
                $routeID,
                $navItem->getRoutingArgs()
            );
            
            if ($navItem->isCurrent())
            {
                $anchorClass = 'current';
            }
            
            if ($navItem->isSection())
            {
                $listClass = 'section';
            }
            
            $string .= "
                <li class=\"{$listClass}\">
                    <a href=\"{$uri}\" class=\"{$anchorClass}\">
                        {$title}
                    </a>
                </li>";
        }
        
        return $string;
    }
}