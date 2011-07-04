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
 * Controller for Carrot's user guide
 * 
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Guide;

use Carrot\Core\Response;
use Carrot\Core\Router;

class Controller
{   
    /**
     * Constructs the guide controller.
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
    
    public function getGuideResponse(array $segments = array())
    {
        if (empty($segments))
        {
            $responseBody = $this->view->renderGuideListPage();
            $this->response->setBody($responseBody);
            return $this->response;
        }
        
        try
        {
            $guidePageContent = $this->model->getGuidePageContent($segments);
            $responseBody = $this->view->renderGuidePage($guidePageContent);
            $this->response->setBody($responseBody);
            return $this->response;
        }
        catch (GuideNotFoundException $exception)
        {
            $responseBody = $this->view->renderGuideNotFoundPage();
            $this->response->setStatus(404);
            $this->response->setBody($responseBody);
            return $this->response;
        }
    }
}