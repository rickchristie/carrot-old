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
 * MySQLi Statement
 * 
 * asdf
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Database\MySQLi;

use MySQLi;
use MySQLi_STMT;

class Statement extends MySQLi_STMT
{
    /**
     * @var string Statement string with placeholders, injected during construction.
     */
    protected $queryWithPlaceholders;
    
    /**
     * @var string Processed statement string, used in constructing the \MySQLi_STMT object, placeholders replaced with '?'.
     */
    protected $query;
    
    /**
     * @var array List of placeholders with the colon (:) prefix, extracted from {@see $statementStringWithPlaceholders}.
     */
    protected $placeholders;
    
    /**
     * @var array List of placeholders with 'blob' data type, set by the user - see {@see }.
     */
    protected $blobParams;
    
    /**
     * @var bool If set to true, any subsequent execution that fails/returns false will trigger an exception.
     */
    protected $throwExceptionWhenExecutionFails;
    
    /**
     * Constructor.
     *
    // ---------------------------------------------------------------
     * Extending the 
     * 
     * @param string $statementString
     * @param string $statementStringWithPlaceholders
     * @param string $placeholders
     *
     */
    public function __construct(MySQLi $mysqli, $query, $queryWithPlaceholders, array $placeholders)
    {
        parent::__construct($mysqli, $query);
    }
    
    /**
     * Execute the query with the placeholders.
     *
     */
    public function execute()
    {
        
    }
    
    /**
     * Disable binding parameters from outside.
     * 
    // ---------------------------------------------------------------
     * Since this class uses placeholders 
     *
     */
    public function bind_param()
    {
        
    }
    
    /**
     * Disable binding results from outside.
     *
     */
    public function bind_result()
    {
        
    }
}