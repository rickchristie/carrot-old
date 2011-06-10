<?php

namespace Carrot\Core;

class Autoloader
{
    /**
     * @var string Absolute path to the PHP file that contains
     */
    protected $autoloader_file_path;
    
    /**
     * @var string Absolute path (with trailing slash) to the default vendor directory, for classes that follows PSR-0 final proposal.
     */
    protected $vendor_directory;
    
    /**
     * Constructs
     *
     */
    public function __construct($autoloader_file_path, $vendor_directory)
    {
        
    }
    
    /**
     * Require the autoloader file path.
     *
     */
    public function register()
    {
        
    }
    
    /**
     * Register
     *
     */
    public function registerPSR()
    {
        
    }
}