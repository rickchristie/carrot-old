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

class View
{
    /**
     * @var Model Instance of the guide model.
     */
    protected $model;
    
    /**
     * Constructs the guide view.
     * 
     * @param Model $model Instance of the guide model
     * 
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
    /**
     * Returns the complete html of the guide page in string.
     * 
     * 
     * 
     * @param string $guidePageContent The guide page content, taken from the model.
     * @return string The complete guide page html, ready to be displayed.
     *
     */
    public function renderGuidePage($guidePageContent)
    {
        $guidePage = $this->getHeader();
        $guidePage .= $this->getSidebar();
        $guidePage .= $guidePageContent;
        $guidePage .= $this->getFooter();
        return $guidePage;
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    public function renderGuideListPage()
    {
        $guideList = $this->model->getGuideList();
        
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    public function renderGuideNotFoundPage()
    {
        return 'page not found!';
    }
    
    /**
     * Returns the HTML header string.
     * 
     * @return string
     * 
     */
    protected function getHeader()
    {
        ob_start();
        ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
        <html>
        <head>
        	<title>Conforming HTML 4.01 Strict Template</title>
        	<style type="text/css">
        	body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px }
        	</style>
        </head>
        <body>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    protected function getFooter()
    {
        ob_start()
        ?>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    protected function getSidebar()
    {
        
    }
}