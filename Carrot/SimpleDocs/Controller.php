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
 * SimpleDocs Controller
 * 
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\SimpleDocs;

use Carrot\Core\Response;
use Carrot\Core\Router;
use Carrot\SimpleDocs\PageNotFoundException;

class Controller
{   
    /**
     * Constructs the controller.
     * 
     * @param Response $response The response object to be returned.
     * 
     */
    public function __construct(View $view, Model $model, Response $response)
    {
        $this->model = $model;
        $this->view = $view;
        $this->response = $response;
    }
    
    /**
     * Main SimpleDoc method.
     * 
     * There is only one method 
     * 
     * @return Response
     * 
     */
    public function getResponse($topicID, $pageID)
    {
        if (empty($topicID) && empty($pageID))
        {
            $topicID = $this->model->getDefaultTopicID();
            $pageID = $this->model->getDefaultPageID($topicID);
        }
        
        try
        {
            $page = $this->model->getPage($topicID, $pageID);
            $responseBody = $this->view->renderPage($topicID, $pageID, $page['title'], $page['content']);
        }
        catch (PageNotFoundException $exception)
        {
            $responseBody = $this->view->renderPageNotFound();
            $this->response->setStatus(404);
        }
        
        $this->response->setBody($responseBody);
        return $this->response;
    }
}