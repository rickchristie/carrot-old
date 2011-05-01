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
 * Template
 * 
 * This class contains the method to load a PHP template file, along with additional
 * utility functions that are frequently used in PHP template files. Your template
 * file name must not contain the '/' character since it is used to denote directory
 * separator.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Helper;

class Template
{
    /**
     * @var string Absolute path to directory containing the template files, without trailing slash.
     */
    protected $template_directory;
    
    /**
     * @var string The site's base URL, used for constructing URLs. With trailing slash.
     */
    protected $base_url;
    
    /**
     * Constructs a template helper.
     * 
     * @param string $template_directory Absolute path to directory containing the template files, without trailing slash.
     * @param string $base_url Base URL to the site, with trailing slash.
     *
     */
    public function __construct($template_directory, $base_url)
    {
        $this->template_directory;
        $this->base_url;
    }
    
    /**
     * Loads a template file based on template name.
     *
     * 'Template name' is a reference used to denote the file name. The '/' character
     * is interpreted as the directory separator, so make sure you don't use it in your
     * template file name. Pass variables to template by using associative arrays, access
     * them in your template using their array index name.
     *
     * <code>
     * // Will load /path/to/template/directory/struct/header.php
     * $header = $template->load('struct/header', array('foo' => 'value', 'bar' => 'value'));
     * </code>
     *
     * @param string $template_name Template name, could contain slashes.
     * @return array $variables Associative array containing variables to be passed to the template.
     *
     */
    public function load($template_name, $variables = array())
    {
        $abs_path_to_file = $this->determineAbsolutePathToTemplateFile($template_name);
        
        
    }
    
    /**
     * A wrapper for htmlentities with ENT_QUOTES.
     *
     * 
     *
     */
    public function clean()
    {
        
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     * 
     */
    public function baseURL()
    {
        return $this->base_url;
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    public function siteURL($segments)
    {
        
    }
    
    /**
     * Determines the absolute path to a template file.
     * 
     * 
     * 
     */
    protected function determineAbsolutePathToTemplateFile($template_name)
    {
        
    }
}