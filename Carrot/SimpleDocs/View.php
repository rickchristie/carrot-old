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
 * SimpleDoc's View
 * 
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\SimpleDocs;

use Carrot\Core\Interfaces\RouterInterface;

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
    public function __construct(Model $model, RouterInterface $router, $routeID)
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
        $renderedPage = $this->getHeader($pageTitle);
        $renderedPage .= $this->getSidebar($currentTopicID, $currentPageID);
        $renderedPage .= $this->wrapContent($pageContent);
        $renderedPage .= $this->getFooter();
        return $renderedPage;
    }
    
    /**
     * Renders page not found into a HTML string.
     * 
     * 
     * 
     */
    public function renderPageNotFound()
    {
        $renderedPage = $this->getHeader('404 Page Not Found!');
        $renderedPage .= $this->getSidebar('', '');
        $renderedPage .= $this->wrapContent($this->getPageNotFoundContent());
        $renderedPage .= $this->getFooter();
        return $renderedPage;
    }
    
    /**
     * Returns the HTML page header tags in a string.
     * 
     * Besides the doctype declaration and opening <head> and <body>
     * tags, this method also echoes out the styles.
     * 
     * @param string $pageTitle The HTML page's title.
     * @return string The HTML page header tags.
     * 
     */
    protected function getHeader($pageTitle)
    {
        ob_start();
        ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
        <html>
        <head>
        	<title><?php echo $pageTitle ?></title>
        	<style type="text/css">
        	body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #333; line-height: 1.5 }
        	#wrapper { width: 800px; margin: 100px auto 100px auto; }
        	#sidebar { width: 200px; float: left; font-size: 12px; }
        	#sidebar ul { margin: 0 0 23px 0; padding: 0; list-style-type: none; line-height: 1.3; }
        	#sidebar ul li { margin: 7px 0; }
        	#sidebar a { text-decoration: none; color: #7a7a7a; border: none; }
        	#sidebar a.current { color: #000; }
        	#sidebar a:hover { color: #000; }
        	#sidebar h3 { margin: 0 0 12px 0; font-size: 15px; }
        	#content { width: 540px; margin-left: 230px }
        	#content ul { margin: 20px 0 22px 0; padding: 0 0 0 0; list-style-type: none; }
        	#content ul li { margin: 8px 0; padding: 0 0 0 40px; background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAPxQTFRF/Pn2+PDo5820tWUXtmca9uziyGYJ/2kE/3UF2E8G/2gG/5UJzJVg+/fz03UI004H01IG6XEI4HoH4ODPz08GuW4ms2ESls6l1mMI797Ow+DKLJVICoMp4loF6VAF+fPu/34FknYquVwI5n4G/40I/v3869bCzZdj2bCISKli1uvbZ7R7FYIy/5EIuWEI71MEwX49+vXx/5oJ/1kF2lUF4e/l028I2+zg6tO8/3YH48WoEIQp0J1rt2og8fjzyFEH/4oH/4MI/4EI+PHrxFsItGMVemgUzujV7drI2HMIE4Ix4vHm/10EuGwj4L+fwHw6/50Iv3o3ypFa////MSaHZAAAAFR0Uk5T//////////////////////////////////////////////////////////////////////////////////////////////////////////////8AU/dy0QAAAI5JREFUeNpczzUWw0AUQ9FvCjOzw8zMzJxo/3tJlbHHKu9R8wiGEW5GqNbCJw4Qi1eWSkeDwTUxyzSlsfYY1Y+udy4UZQB/Nt3o+h4CA1okLWv7pU1/wFnum7f54ZwBlJfnPtmYRAagUqBl88oqA+ApWXsRhw4gfFepfVEHgBh0fwrEhZUPU+Jbd86fAAMAJ31C2sCnrroAAAAASUVORK5CYII=) 18px 0px no-repeat; }
        	h1 { font-size: 26px; margin: 20px 0 20px 0; }
        	h2 { font-size: 19px; margin: 20px 0 20px 0; }
        	h3 { font-size: 15px; }
        	h1, h2, h3, h4, h5, h6 { line-height: 1.2; font-weight: normal; color: #000; }
        	code { font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace; font-size: 90%; }
    		pre { font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace; font-size: 12px; display: block; overflow: auto; background: #edf0f3; border: 1px solid #cbd3db; line-height: 17px; -moz-border-radius: 5px; border-radius: 5px; padding: 15px; margin: 25px 0; }
            p { margin: 18px 0; }
            em { font-style: italic }
            blockquote { margin: 18px 0 18px 40px; font-size: 14px; color: #666; }
            blockquote cite { font-size: 12px; font-style: normal; }
            cite { font-style: normal; }
            a { color: #d56600; text-decoration: none; }
            a:visited { color: #b05400; }
    		a:hover { color: #ff843a; border-bottom: 1px solid #dfb97b; }
    		a:active { color: #f13900; }
        	.clearfix:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }
            .clearfix { display: inline-block; }
            /* Hides from IE-mac \*/ * html .clearfix {height: 1%;} .clearfix {display: block;} /* End hide from IE-mac */
        	</style>
        </head>
        <body>
            <div id="wrapper" class="clearfix">
        <?php
        return ob_get_clean();
    }
    
    /**
     * Returns the HTML page closing tags in a string.
     *
     * Mainly just closes the opened <html> and <body> tags.
     * 
     * @return string HTML page closing tags.
     *
     */
    protected function getFooter()
    {
        return '
            </div> <!-- #wrapper -->
        </body>
        </html>';
    }
    
    /**
     * Gets the sidebar page navigation.
     * 
     * Gets the complete page list from Model::getcompletePageList()
     * and reformats using generateNavigationArray() before generating
     * the sidebar.
     *
     * @return string The sidebar page navigation HTML.
     * 
     */
    protected function getSidebar($currentTopicID, $currentPageID)
    {   
        $completePageList = $this->model->getcompletePageList();
        $navArray = $this->generateNavigationArray($completePageList, $currentTopicID, $currentPageID);
        ob_start();
        ?>
        <div id="sidebar">
            <?php foreach ($navArray as $topicName => $pages): ?>
                <h3><?php echo $topicName ?></h3>
                <ul>
                    <?php foreach ($pages as $page): ?>
                    <li><a class="<?php echo $page['class'] ?>" href="<?php echo $page['url'] ?>">
                        <?php if ($page['class'] == 'current') echo '@'; ?>
                        <?php echo $page['title'] ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
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
            $navPageInfo = array();
            
            foreach ($topic['pages'] as $pageID => $page)
            {
                $navPageInfo['title'] = htmlspecialchars($page['title'], ENT_QUOTES);
                $navPageInfo['url'] = htmlspecialchars($this->router->getURL($this->routeID, array('topicID' => $topicID, 'pageID' => $pageID)));
                $navPageInfo['class'] = '';
                
                if ($topicID == $currentTopicID && $pageID == $currentPageID)
                {
                    $navPageInfo['class'] = 'current';
                }
                
                $navArray[$formattedTopicName][] = $navPageInfo;
            }
        }
        
        return $navArray;
    }
    
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
    
    /**
     * Wraps the page content in the proper container.
     *
     * @param string $pageContent The page content to be wrapped.
     * @return string Wrapped page content.
     *
     */
    protected function wrapContent($pageContent)
    {
        return '<div id="content">' . $pageContent . '</div>';
    }
}