<?php

namespace Carrot\Autopilot;

/**
 * Responsible for logging all operations on Autopilot.
 * 
 * Automatic dependency injection, although convenient, will be
 * harder to debug than hard wired dependency injection. To help
 * make debugging rules easier, Autopilot automatically logs
 * its actions with this object.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class AutopilotLog
{
    /**
     * List of reference IDs that has been logged.
     * 
     * @var array $referenceIds
     */
    private $referenceIds;
    
    /**
     * Holds all of the logs messages.
     * 
     * Has the following structure:
     * 
     * <pre>
     * $data = array(
     *     0 => array(
     *         'reference' => 'Carrot\MySQLi\MySQLi@:Singleton',
     *         'instantiator' => array(
     *             'Cache: Carrot\Autopilot\RuntimeCache: Nothing found.',
     *             ...
     *         ),
     *         'setter' => array(
     *             
     *         )
     *     ),
     *     ...
     * )
     * </pre>
     * 
     * 
     * @var array $data
     */
    private $data = array();
    
    /**
    //---------------------------------------------------------------
     * Used to make sure messages go into the right index.
     * 
     * @var int $currentIndex
     */
    private $currentIndex = -1;
    
    /**
     * @var bool 
     */
    private $isLoggingExtraInfo = TRUE;
    
    /**
    //---------------------------------------------------------------
     * 
     *
     */
    public function logExtraInfo($isLoggingExtraInfo)
    {
        $this->isLoggingExtraInfo = $isLoggingExtraInfo;
    }
    
    public function logInstantiatorCacheNotFound($referenceId)
    {
        
    }
    
    public function logInstantiatorCacheFound()
    {
        
    }
    
    public function logSetterCacheNotFound($referenceId)
    {
        
    }
    
    public function logSetterCacheFound()
    {
        
    }
    
    public function logConsultInstantiatorRulebookNotFound()
    {
        
    }
    
    public function logConsultInstantiatorRulebookFound()
    {
        
    }
    
    public function logConsultSetterRulebookNotFound()
    {
        
    }
    
    public function logConsultSetterRulebookFound()
    {
        
    }
    
    public function logUsingInstantiator()
    {
        
    }
    
    public function logUsingSetter()
    {
        
    }
    
    /**
     * Renders the logging messages into HTML string, ready to be
     * printed.
     * 
     * @return string
     *
     */
    public function renderToHtml()
    {
        
    }
    
    public function renderToText()
    {
        
    }
}