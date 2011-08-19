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
 * SimpleDocs Model
 *
 * This model class encapsulates the SimpleDocs data and behavior.
 * Because this is a very simple solution, it doesn't support
 * other storage options than files, hence we don't need to
 * separate the data access layer from the model.
 *
 * Here are a couple of business rules:
 * 
 * <ul>
 *  <li>Every page must be filed under a topic.</li>
 *  <li>Topics cannot be nested.</li>
 *  <li>Page files are stored inside a storage directory, which is
 *   injected to this class at construction.</li>
 *  <li>Each topic is represented by a directory inside the
 *   storage directory. The name of the topic directory is the
 *   name of the topic. You can add a number prefix for sorting,
 *   but it is not required.</li>
 *  <li>Pages filed under that topic is saved inside the topic
 *   directory as HTML files, the file name of the HTML file is
 *   the title of the page. You can add a number prefix for
 *   sorting, but it is not mandatory.</li>
 *  <li>
 *  <li>Each page has an ID, the same goes with topic. Users do
 *   not need to give each page/topic an ID - SimpleDocs will
 *   generate them from the file/directory name. These ID are
 *   URL friendly and you are recommended to use them in your
 *   routes.</li> 
 * </ul>
 * 
 * For example, with the below storage directory contents (plus
 * sign is directory, minus sign is file):
 *
 * <code>
 * + A. General Topics
 *   - 1. Welcome To Carrot.html
 *   - 2. 
 * </code>
 * 
 * Your HTML templates will not be parsed so PHP tags will not
 * work inside the templates. You can change the file suffix
 * (defaults to '.html') by injecting a different string at
 * construction.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Docs;

use InvalidArgumentException;

class Model
{
    /**
     * @var string Absolute path to the directory that contains the page files, without trailing directory separator.
     */
    protected $storageDirectory;
    
    /**
     * @var string Suffix to the page file name, added after the page title.
     */
    protected $pageFileSuffix;
    
    /**
     * @var array Complete list of pages in an array.
     */
    protected $completePageList = NULL;
    
    /**
     * Construct the model.
     * 
     * Inject the location of the storage directory:
     * 
     * <code>
     * $model = new Model('/absolute/path/to/storage');
     * </code>
     *
     * Will throw InvalidArgumentException if the storage directory
     * injected doesn't exist or the path provided does not point
     * to a directory.
     *
     * @throws InvalidArgumentException
     * @param string $storageDirectory Absolute path to the directory that contains the page files.
     * @param string $pageFileSuffix The suffix to the page file name, added after the page title.
     *
     */
    public function __construct($storageDirectory = NULL, $pageFileSuffix = '.html')
    {
        if (!$storageDirectory)
        {
            $storageDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'Storage';
        }
        
        $this->pageFileSuffix = $pageFileSuffix;
        $realpath = realpath($storageDirectory);
        
        if ($realpath === FALSE)
        {
            throw new InvalidArgumentException("Failed to construct SimpleDocs model. Storage directory provided ({$storageDirectory}) is not valid.");
        }
        
        if (!is_dir($realpath))
        {
            throw new InvalidArgumentException("Failed to construct SimpleDocs model. '{$storageDirectory}' is not a directory.");
        }
        
        $this->storageDirectory = $realpath;
        $this->generatecompletePageList();
    }
    
    /**
     * Retrieve page with the provided topic ID and page ID.
     * 
     * Example page retrieval:
     *
     * <code>
     * $page = $model->getPage('general-topics', 'quick-introduction-to-carrot');
     * </code>
     *
     * This method returns an array that contains the page's title and
     * content:
     *
     * <code>
     * $page = array
     * (
     *     'title' => 'Quick Introduction To Carrot',
     *     'content' => '<h1>The contents</h1>...'
     * );
     * </code>
     *
     * If the page does not exist this method will throw
     * PageNotFoundException.
     * 
     * @throws PageNotFoundException
     * @param string
     * @param string
     * @return array Contains the page title and content.
     * 
     */
    public function getPage($topicID, $pageID)
    {
        if (!isset($this->completePageList[$topicID]['pages'][$pageID]))
        {
            throw new PageNotFoundException("SimpleDocs model error in retrieving the page. Page ID '{$pageID}' under topic ID '{$topicID}' does not exist.");
        }
                
        return array
        (
            'title' => $this->completePageList[$topicID]['pages'][$pageID]['title'],
            'content' => file_get_contents($this->completePageList[$topicID]['pages'][$pageID]['path'])
        );
    }
    
    /**
     * Returns the complete hierarchical list of pages in an array:
     * 
     * 
     * 
     * <code>
     * $completePageList = array
     * (
     *     
     * );
     * </code>
     * 
     * @return array List of pages in hierarchical array.
     *
     */
    public function getcompletePageList()
    {
        return $this->completePageList;
    }
    
    public function getDefaultTopicID()
    {
        reset($this->completePageList);
        return key($this->completePageList);
    }
    
    public function getDefaultPageID($topicID)
    {
        if (!isset($this->completePageList[$topicID]['pages']))
        {
            throw new InvalidArgumentException("SimpleDocs model error in getting default page ID. Topic ID '{$topicID}' does not exist.");
        }
        
        reset($this->completePageList[$topicID]['pages']);
        return key($this->completePageList[$topicID]['pages']);
    }
    
    protected function generatecompletePageList()
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->storageDirectory));
        $iterator->setMaxDepth(1);
        
        foreach ($iterator as $item)
        {
            $pageTitle = $item->getFileName();
            
            if (!$this->isValidPageFile($pageTitle))
            {
                continue;
            }
            
            $topicName = $iterator->getSubPath();
            $filePath = $item->getPathName();
            $topicName = $this->removeNumberPrefix($topicName);
            $pageTitle = $this->removeFileSuffix($this->removeNumberPrefix($pageTitle));
            $topicID = $this->transformToID($topicName);
            $pageID = $this->transformToID($pageTitle);
            
            if (!isset($this->completePageList[$topicID]))
            {
                $this->completePageList[$topicID] = array
                (
                    'name' => $topicName,
                    'pages' => array()
                );
            }
            
            $this->completePageList[$topicID]['pages'][$pageID] = array
            (
                'title' => $pageTitle,
                'path' => $filePath
            );
        }
    }
    
    protected function removeFileSuffix($pageTitle)
    {
        return substr($pageTitle, 0, strlen($pageTitle) - strlen($this->pageFileSuffix));
    }
    
    protected function removeNumberPrefix($topicName)
    {
         return trim(preg_replace('/^[0-9A-Za-z]+\. /', '', $topicName));
    }
    
    protected function transformToID($string)
    {
        return strtolower(str_replace(' ', '-', $string));
    }
    
    /**
     * Checks if the given file path is a valid page file.
     *
     * It just checks the file for the appropriate suffix, according
     * to the string injected at object construction.
     *
     * @param string $filePath Absolute path to the file to be checked.
     * @return bool TRUE if it is a valid page file, FALSE otherwise.
     *
     */
    protected function isValidPageFile($filePath)
    {
        return (substr($filePath, - (strlen($this->pageFileSuffix))) == $this->pageFileSuffix);
    }
}