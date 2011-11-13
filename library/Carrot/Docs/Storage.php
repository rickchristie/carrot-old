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
 * Docs storage.
 *
 * Represents a documents storage and provides ways to access
 * the documents inside the storage. This class reads folders and
 * .html files inside a directory and creates a hierarchical docs
 * structure from it.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Docs;

use Exception,
    InvalidArgumentException,
    DirectoryIterator;

class Storage
{
    /**
     * @var string The directory that contains the document files,
     *      without trailing directory separator.
     */
    protected $rootDirectory;
    
    /**
     * Constructor.
     * 
     * If no path was given, the root directory will default to:
     *
     * <code>
     * __DIR__ . DIRECTORY_SEPARATOR . 'Files'
     * </code>
     * 
     * The directory contains documentation files in HTML format and
     * can contain as many folders as you need. You can provide
     * 'section.html' file on each directory to be loaded when that
     * section is requested.
     * 
     * @param string $rootDirectory The directory that contains the
     *        document files.
     *
     */
    public function __construct($rootDirectory = NULL)
    {
        if ($rootDirectory == NULL)
        {
            $rootDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'Files';
        }
        
        $this->setRootDirectory($rootDirectory);
    }
    
    /**
     * Set the directory that contains the document files.
     *
     * @throws InvalidArgumentException If the root directory given
     *         is not a valid path or it's not a directory.
     * @param string $rootDirectory The directory that contains the
     *        document files.
     *
     */
    protected function setRootDirectory($rootDirectory)
    {
        $rootDirectoryAbsolute = realpath($rootDirectory);
        
        if ($rootDirectoryAbsolute == FALSE)
        {
            throw new InvalidArgumentException("Storage error in instantiation. The given path '{$rootDirectory}' does not exist.");
        }
        
        if (is_dir($rootDirectoryAbsolute) == FALSE)
        {
            throw new InvalidArgumentException("Storage error in instantiation. The given path '{$rootDirectory}' is not directory.");
        }
        
        $this->rootDirectory = $rootDirectoryAbsolute;
    }
    
    /**
     * Get an instance of page from the given hierarchical path.
     * 
     * The path array contains the hierarchical path to the
     * documentation. The array contains item IDs. Example:
     *
     * <code>
     * $path = array(
     *     '1-introduction',
     *     '1-calculator-tutorial',
     *     '1-creating-your-controller'
     * );
     * </code>
     * 
     * @param array $pagePathArray The hierarchical path to the
     *        documentation page to retrieve.
     * @return Page|FALSE The documentation page, or FALSE if failed.
     *
     */
    public function getPage(array $pagePathArray)
    {   
        if (empty($pagePathArray))
        {
            return $this->getIndexPage();
        }
        
        $pageInfo = $this->getPageInfo($pagePathArray);
        
        if (!is_array($pageInfo))
        {
            return FALSE;
        }
        
        return new Page(
            $pageInfo['title'],
            $pageInfo['content'],
            $pageInfo['parentSections'],
            $pageInfo['navigation']
        );
    }
    
    /**
     * Get the page containing the index to be returned.
     * 
     * Will try to load the contents of the 'section.html' file in
     * the route, if it fails, will simply return an empty body with
     * the root navigation.
     * 
     * @return Page
     *
     */
    public function getIndexPage()
    {
        $parentSections = array();
        $navigation = $this->getNavigationItemsFromDirectory($this->rootDirectory, array());
        $title = '';
        $content = $this->getSectionIndexContent($this->rootDirectory);
        return new Page(
            $title,
            $content,
            $parentSections,
            $navigation
        );
    }
    
    /**
     * Get information about the page from the given hierarchical
     * path.
     * 
     * Assumes the array is not empty. The structure of the array
     * returned is as follows:
     *
     * <code>
     * $pageInfo = array(
     *     'parentSections' => $parentSections,
     *     'navigation' => $navigation,
     *     'title' => $title,
     *     'content' => $content
     * );
     * </code>
     *
     * The array $parentSections contains the page's parent sections.
     * Each section is represented by a {@see NavigationItem}
     * instance:
     *
     * <code>
     * $parentSections = array(
     *     0 => $sectionA,
     *     1 => $sectionB,
     *     2 => $sectionC
     *     ...
     * );
     * </code>
     * 
     * The array $navigation contains the list of accessible items
     * for the current open section, be it a page or another section,
     * but with the item ID as the indexes:
     *
     * <code>
     * $navigation = array(
     *     '1-introduction' => $navItemA,
     *     '2-autoloading' => $navItemB,
     *     '3-dependency-injection' => $navItemC,
     *     ...
     * );
     * </code>
     * 
     * @param array $pagePathArray The hierarchical path to the
     *        documentation page to retrieve.
     *
     */
    protected function getPageInfo(array $pagePathArray)
    {
        $parentSections = array();
        $navigation = array();
        $title = '';
        $content = '';
        $directory = $this->rootDirectory;
        $segmentsTraversed = array();
        $highestLevel = count($pagePathArray) - 1;
        
        foreach ($pagePathArray as $level => $itemID)
        {   
            $isHighestLevel = ($level == $highestLevel);
            $availableItems = $this->getNavigationItemsFromDirectory(
                $directory,
                $segmentsTraversed
            );
            
            if (!array_key_exists($itemID, $availableItems))
            {
                return FALSE;
            }
            
            $navItem = $availableItems[$itemID];
            
            if ($isHighestLevel)
            {
                if ($navItem->isSection())
                {
                    $parentSections[] = $navItem;
                    $segmentsTraversed[] = $itemID;
                    $directory = $navItem->getRealPath();
                    $navigation = $this->getNavigationItemsFromDirectory(
                        $directory,
                        $segmentsTraversed
                    );
                    
                    $content = $this->getSectionIndexContent($directory);
                }
                else
                {
                    $navigation = $availableItems;
                    $navigation[$itemID]->markAsCurrent();
                    $content = $this->getFileContent($navItem->getRealPath());
                }
                
                return array(
                    'parentSections' => $parentSections,
                    'navigation' => $navigation,
                    'title' => $navItem->getTitle(),
                    'content' => $content
                );
            }
            
            $parentSections[] = $navItem;
            $segmentsTraversed[] = $itemID;
            $directory = $navItem->getRealPath();
        }
    }
    
    /**
     * Get the list of navigational items from the given directory.
     * 
     * Iterates through the given directory and creates instances of
     * {@see NavigationItem} from its contents. Uses the given root
     * path segments to construct routing argument arrays of each
     * NavigationItem instance.
     * 
     * The root path segments array structure:
     * 
     * <code>
     * $rootPathSegments = array(
     *     '1-Introduction',
     *     '1-Calculator-Tutorial'
     * );
     * </code>
     * 
     * Returns an array containing instances of NavigationItem:
     *
     * <code>
     * $navigationItems = array(
     *     $navItemA,
     *     $navItemB,
     *     $navItemC,
     *     ...
     * );
     * </code>
     * 
     * @param string $directory Absolute path to the directory to
     *        search the files from.
     * @param array $rootPathSegments The root path segments leading
     *        to the given directory, to be used in constructing
     *        routing arguments on each NavigationItem instances.
     * @return array Array containing instances of
     *         {@see NavigationItem}. Returns an empty array if it
     *         fails to retrieve data from the directory.
     *
     */
    protected function getNavigationItemsFromDirectory($directory, array $rootPathSegments)
    {
        $items = array();
        
        try
        {
            $iterator = new DirectoryIterator($directory);
        }
        catch (Exception $exception)
        {
            return $items;
        }
        
        foreach ($iterator as $key => $content)
        {
            $navItem = NULL;
            
            if ($content->isFile())
            {
                $fileName = $content->getFilename();
                $realPath = $content->getPathname();
                $navItem = $this->getNavigationItemFromFile(
                    $fileName,
                    $realPath,
                    $rootPathSegments
                );
            }
            
            if ($content->isDir() AND $content->isDot() == FALSE)
            {
                $fileName = $content->getFilename();
                $realPath = $content->getPathname();
                $navItem = $this->getNavigationItemFromDirectory(
                    $fileName,
                    $realPath,
                    $rootPathSegments
                );
            }
            
            if ($navItem instanceof NavigationItem)
            {
                $items[$navItem->getItemID()] = $navItem;
            }
        }
        
        return $items;
    }
    
    /**
     * Get a {@see NavigationItem} instance from the given file name
     * and file path.
     * 
     * Only accepts '.html' files with the following pattern as the
     * file name:
     *
     * <code>
     * 1. File Name.html
     * A. File Name.html
     * a. File Name.html
     * </code>
     * 
     * @see getNavigationItemsFromDirectory()
     * @param string $fileName The name of the file.
     * @param string $realPath The real path to the file.
     * @param array $rootPathSegments The root path segments leading
     *        to the given file, to be used in constructing routing
     *        arguments array.
     * @return NavigationItem
     *
     */
    protected function getNavigationItemFromFile($fileName, $realPath, array $rootPathSegments)
    {
        $fileNamePattern = '/^([A-Za-z0-9]+\\. (.+))\.html$/uD';
        $replaceDotAndSpacesPattern = '/[\\. ]+/u';
        
        if (preg_match($fileNamePattern, $fileName, $matches))
        {
            $title = $matches[2];
            $itemID = preg_replace($replaceDotAndSpacesPattern, '-', $matches[1]);
            $routingArgs = $rootPathSegments;
            $routingArgs[] = $itemID;
            return new NavigationItem(
                $title,
                'doc',
                $routingArgs,
                $realPath
            );
        }
    }
    
    /**
     * Get a {@see NavigationItem} instance from the given directory.
     *
     * All directory names are accepted.
     * 
     * @see getNavigationItemsFromDirectory()
     * @param string $fileName The name of the file.
     * @param string $realPath The real path to the file.
     * @param array $rootPathSegments The root path segments leading
     *        to the given file, to be used in constructing routing
     *        arguments array.
     * @return NavigationItem
     *
     */
    protected function getNavigationItemFromDirectory($directoryName, $realPath, array $rootPathSegments)
    {
        $directoryPattern = '/^([A-Za-z0-9]+\\. (.+))$/uD';
        $replaceDotAndSpacesPattern = '/[\\. ]+/u';
        
        if (preg_match($directoryPattern, $directoryName, $matches))
        {
            $title = $matches[2];
            $itemID = preg_replace($replaceDotAndSpacesPattern, '-', $matches[1]);
            $routingArgs = $rootPathSegments;
            $routingArgs[] = $itemID;
            return new NavigationItem(
                $title,
                'section',
                $routingArgs,
                $realPath
            );
        }
    }
    
    /**
     * Get the content for section index.
     * 
     * Will try to get an 'section.html' file on the given directory.
     * If none found, an empty string will be returned instead.
     * 
     * @param string $directory The physical counterpart of the
     *        section whose index content we wanted to get, without
     *        trailing directory separator.
     * @return string
     *
     */
    protected function getSectionIndexContent($directory)
    {
        $filePath = $directory . DIRECTORY_SEPARATOR . 'section.html';
        return $this->getFilecontent($filePath);
    }
    
    /**
     * Get the content from the give file.
     * 
     * If the file doesn't exist, will return an empty string
     * instead.
     *
     * @param string $filePath Path to the file to get.
     *
     */
    protected function getFileContent($filePath)
    {
        if (!file_exists($filePath))
        {
            return '';
        }
        
        return file_get_contents($filePath);
    }
}