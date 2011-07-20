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
 * View
 * 
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Docs;

use Carrot\Core\Router;

class View
{
    /**
     * @var Model Instance of the SimpleDocs model, used to obtain page data.
     */
    protected $model;
    
    /**
     * @var RouterInterface Instance of the router used for this request, used to do two-way routing.
     */
    protected $router;
    
    /**
     * @var string ID of the route registration that is responsible for routing to this view instance's controller.
     */
    protected $routeID;
    
    /**
     * Constructs the view.
     * 
     * @param Model $model Instance of the SimpleDocs model, used to obtain page data.
     * @param RouterInterface $router Instance of the router used for this request.
     * 
     */
    public function __construct(Model $model, Router $router, $routeID)
    {
        $this->model = $model;
        $this->router = $router;
        $this->routeID = $routeID;
    }
    
    /**
     * Renders the page into a HTML string.
     * 
     * 
     * 
     * @param string $currentTopicID
     * @return string The complete page HTML, ready to be displayed.
     *
     */
    public function renderPage($currentTopicID, $currentPageID, $pageTitle, $pageContent)
    {
        $completePageList = $this->model->getcompletePageList();
        $navArray = $this->generateNavigationArray($completePageList, $currentTopicID, $currentPageID);
        return $this->getTemplate($pageTitle, $navArray, $pageContent);
    }
    
    protected function getTemplate($pageTitle, array $navArray, $pageContent)
    {
        ob_start();
        require __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'default.php';
        return ob_get_clean();
    }
    
    /**
     * Renders page not found into a HTML string.
     * 
     * 
     * 
     */
    public function renderPageNotFound()
    {
        $completePageList = $this->model->getcompletePageList();
        $navArray = $this->generateNavigationArray($completePageList, '', '');
        $pageContent = $this->getPageNotFoundContent();
        $pageTitle = 'Oops! 404 Page Not Found Error!';
        return $this->getTemplate($pageTitle, $navArray, $pageContent);
    }
    
    /**
     * Formats the complete page list into a navigation array.
     *
     * The navigation array is used to construct the sidebar. After
     * the complete page list array is retrieved, it is reformatted and
     * escaped by this method.
     *
     * Example output:
     *
     * <code>
     * $navArray = array
     * (
     *     'Clean Topic Name' => array
     *     (
     *         0 => array
     *         (
     *             'title' => 'Clean Page Title',
     *             'url' => 'http://clean.url/to/page/',
     *             'class' => 'current'
     *         ),
     *         ...
     *     ),
     *     ...
     * );
     * </code>
     *
     * For the currently being rendered page, the 'class' index
     * will contain 'current', otherwise it will be empty.
     * 
     * @param array $completePageList The complete page list in a hierarchical array.
     * @param string $currentTopic The topic name of the page currently being rendered.
     * @param string $currentPageTitle The title of the page currently being rendered.
     * @return array The formatted and escaped navigation information.
     *
     */
    protected function generateNavigationArray(array $completePageList, $currentTopicID, $currentPageID)
    {
        $navArray = array();
        
        foreach ($completePageList as $topicID => $topic)
        {
            $formattedTopicName = htmlspecialchars($topic['name'], ENT_QUOTES);
            $navArrayPage = array();
            
            foreach ($topic['pages'] as $pageID => $page)
            {
                $navArrayPage['title'] = htmlspecialchars($page['title'], ENT_QUOTES);
                $navArrayPage['url'] = htmlspecialchars($this->router->getURL($this->routeID, array('topicID' => $topicID, 'pageID' => $pageID)));
                $navArrayPage['class'] = '';
                
                if ($topicID == $currentTopicID && $pageID == $currentPageID)
                {
                    $navArrayPage['class'] = 'current';
                }
                
                $navArray[$formattedTopicName][] = $navArrayPage;
            }
        }
        
        return $navArray;
    }
    
    /**
     * asdfsa
     *
     */
    protected function getPageNotFoundContent()
    {
        ob_start();
        ?>
        <h1>Page Does Not Exist</h1>
        <blockquote><p>You just keep pushing. You just keep pushing. I made every mistake that could be made. But I just kept pushing. <cite>- Rene Descartes</cite></p></blockquote>
        <p>We are sorry, but the page you were looking for does not exist. Maybe the link is broken, or maybe the page has been removed. Whatever it is, you can't access it right now. We humbly ask you to consider the <em>real</em> contents available from the links at the sidebar to the left.</p>
        <?php
        return ob_get_clean();
    }
}