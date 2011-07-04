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
 * Model for Carrot's user guide
 * 
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Guide;

use InvalidArgumentException;

class Model
{
    /**
     * @var string Absolute path to the directory that contains the guide files, without trailing directory separator.
     */
    protected $filesDirectory;
    
    /**
     * Construct the guide model.
     * 
     * @param string $filesDirectory Absolute path to the directory that contains the guide files.
     *
     */
    public function __construct($filesDirectory)
    {
        $realpath = realpath($filesDirectory);
        
        if ($realpath === false)
        {
            throw new InvalidArgumentException("Guide model fails to instantiate, guide file directory provided ({$filesDirectory}) is not valid.");
        }
        
        $this->filesDirectory = $realpath;
    }
    
    /**
     * Get the guide page content in string.
     * 
     * Call this method to retrieve the contents of a guide page:
     * 
     * <code>
     * 
     * </code>
     * 
     * Will throw GuideNotFoundException if the guide file is not
     * found.
     * 
     * @throws GuideNotFoundException
     * @param array $segments Array of hierarchical path that points to the guide.
     * @return string Guide page content.
     *
     */
    public function getGuidePageContent(array $segments)
    {
        $guideFilePath = $this->generateGuideFilePath($segments);
        
        if (!file_exists($guideFilePath))
        {
            throw new GuideNotFoundException("Guide model error in getting page content, guide file {$guideFilePath} does not exist.");
        }
        
        ob_start();
        require $guideFilePath;
        return ob_get_clean();
    }
    
    /**
     * Gets the list of available guides in an array.
     * 
     * @return array List of available guides in array.
     * 
     */
    public function getGuideList()
    {
        
    }
    
    /**
     * Generates absolute file path to the guide.
     * 
     * Each segment becomes a path segment and the last segment is
     * appended with '.html' suffix. For example, if the segment array
     * is as follows:
     *
     * <code>
     * $segment = array('carrot', 'hello-world-tutorial');
     * </code>
     * 
     * Will be translated to something like:
     *
     * <code>
     * /files/directory/carrot/hello-world-tutorial.html
     * </code>
     * 
     * @param array $segments Guide hierarchical segments.
     * 
     */
    protected function generateGuideFilePath(array $segments)
    {
        $path = $this->filesDirectory;
        
        foreach ($segments as $index => $segment)
        {
            $path .= DIRECTORY_SEPARATOR . $segment;
        }
        
        return $path . '.html';
    }
}