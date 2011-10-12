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
 * Template Loader
 * 
 * Used to load templates as string. Provides Router,
 * AppRequestURI and Assets by default to the templates.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Helper;

use RuntimeException;

class TemplateLoader
{
    /**
     * @var string Absolute path to the directory that contains the templates, without trailing directory separator.
     */
    protected $templateRootDirectory;
    
    /**
     * @var array Default variables to pass to each template loaded.
     */
    protected $defaultVars;
    
    /**
     * Constructor.
     *
     * Simply pass the template root directory as the constructor
     * parameter:
     *
     * <code>
     * $template = new TemplateLoader(
     *     '/absolute/path/to/template/directory/',
     *     array(
     *         'router' => $router,
     *         'assets' => $assets,
     *         'appRequestURI' => $appRequestURI
     *     )
     * );
     * </code>
     * 
     * @param string $templateRootDirectory Absolute path to the directory that contains the templates.
     * @param array $defaultVars Default variables to pass to each template loaded.
     *
     */
    public function __construct($templateRootDirectory, array $defaultVars = array())
    {
        $templateRootDirectoryFormatted = realpath($templateRootDirectory);
        
        if ($templateRootDirectoryFormatted === FALSE OR !is_dir($templateRootDirectoryFormatted))
        {
            throw new RuntimeException("Template error in instantiating. Either the path '{$templateRootDirectory}' is not a valid path or it is not a directory.");
        }
        
        $this->defaultVars = $defaultVars;
        $this->templateRootDirectory = $templateRootDirectoryFormatted;
    }
    
    /**
     * Gets the router.
     *
     * @return Router $router
     *
     */
    public function getRouter()
    {
        return $this->router;
    }
    
    /**
     * Loads a template file.
     *
     * The PHP template file must be located inside the template
     * directory. The template name is transformed to an absolute
     * file path with regard to the template directory. The slash (/)
     * character in the template name is replaced to directory
     * separator, and the last segment of the template name turns
     * into the file name. So, if as an example the template
     * directory set is 'C:\templates', the following transformations
     * occur:
     * 
     * <code>
     * header -> C:\templates\header.php
     * blog/page -> C:\templates\blog\page.php
     * blog/page/comments -> C:\templates\blog\page\comments.php
     * </code>
     *
     * Pass variables to the template by sending an associative
     * array. Please note that variable name 'context' is reserved.
     * Your variables will override default variables set at this
     * object's construction if they have the same key.
     *
     * <code>
     * $variables = array(
     *     'pageTitle' => 'This is the page title',
     *     'router' => $router
     * );
     * </code>
     * 
     * You can access the variables by their associative index
     * directly. Your template is loaded directly by this class, so it
     * can access this object with '$this' or '$t'. This means you can
     * make use of this object's {@see clean()} method.
     *
     * <code>
     * <h1>
     *     <a href="<?php urlencode($router->getURL('home')) ?>">
     *         <?php echo $t->clean(pageTitle) ?>
     *     </a>
     * </h1>
     * </code>
     * 
     * Throws RuntimeException if the file doesn't exist.
     * 
     * @throws RuntimeException
     * @param string $templateName Name of the template.
     * @param array $variables Associative array of variables to pass.
     * @return string The template, loaded in string.
     *
     */
    public function load($templateName, array $variables = array())
    {
        $filePath = $this->transformToFilePath($templateName);
        
        if (!file_exists($filePath))
        {
            throw new RuntimeException("Template error in loading template. The provided template name ({$templateName}) doesn't have a real physical counterpart ($filePath).");
        }
        
        $variables = array_merge($this->defaultVars, $variables);
        $context = array(
            'filePath' => $filePath,
            'templateName' => $templateName,
            'variables' => $variables
        );
        
        return $this->loadFileAsString($context);
    }
    
    /**
     * Clean a string, so that it can be safely outputted to a HTML file.
     *
     * Runs the string to htmlspecialchars() with ENT_QUOTES enabled.
     * This prevents cross site scripting and makes sure the string
     * you output won't ruin the HTML structure.
     *
     * @param string $string The string to be cleaned.
     * @return string The cleaned string.
     *
     */
    public function clean($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }
    
    /**
     * Transforms a template name to a file path.
     *
     * The slash (/) character in the template name is replaced to
     * directory separator, and the last segment of the template name
     * turns into the file name. So, if as an example the template
     * directory set is 'C:\templates', the following transformations
     * occur:
     * 
     * <code>
     * header -> C:\templates\header.php
     * blog/page -> C:\templates\blog\page.php
     * blog/page/comments -> C:\templates\blog\page\comments.php
     * </code>
     *
     * @param string $templateName The name of the template.
     * @return string The absolute path to the template file.
     *
     */
    protected function transformToFilePath($templateName)
    {
        $templateName = trim($templateName, '/');
        $templateName = str_replace('/', DIRECTORY_SEPARATOR, $templateName);
        return $this->templateRootDirectory . DIRECTORY_SEPARATOR .  $templateName . '.php';
    }
    
    /**
     * Load file as string using output buffering.
     * 
     * Before requiring the files, it extracts variables first so
     * they can be accessed directly. The context array structure as
     * follows:
     * 
     * <code>
     * $context = array(
     *     'filePath' => '/absolute/path/to/file.php',
     *     'templateName' => 'template/name',
     *     'variables' = $variables
     * );
     * </code>
     * 
     * @param array $context Containing information on the file context.
     *
     */
    protected function loadFileAsString(array $context)
    {
        extract($context['variables'], EXTR_SKIP);
        ob_start();
        $t = $this;
        require $context['filePath'];
        return ob_get_clean();
    }
}