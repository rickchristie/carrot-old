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
 * Wrapper to MySQLi
 * 
 * 
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Database\MySQLi;

class MainWrapper
{   
    /**
     * @var MySQLi Instance of MySQLi.
     */
    protected $mysqli;
    
    /**
     * @var array List of previously run query.
     */
    protected $query_history;
    
    /**
     * @var bool If set to true, will throw exception each time a query execution fails/returns false.
     */
    protected $throw_exception_when_query_fails = false;
    
    /**
     * Constructs a MySQLDatabase object, a MySQLi wrapper.
     *
     * Will throw exception if instance of MySQLi given is not connected.
     *
     * @param MySQLi $mysqli Instance of MySQLi (connected).
     *
     */
    public function __construct(\MySQLi $mysqli)
    {
        // Found out that the only reliable way to test if the MySQLi object
        // is connected is by testing MySQLi::server_version, since we can
        // instantiate MySQLi without parameter, which will create a MySQLi
        // object that doesn't have connect_error or connect_errno. Accessing
        // server_version property raises a warning, hence the '@' operator.
        
        if (!@$mysqli->server_version)
        {
            $error_no = $mysqli->connect_errno;
            $error_string = $mysqli->connect_error;
            throw new \RuntimeException("MySQLWrapper object construction error, unable to connect, Error Number: '{$error_no}', Error String: '{$error_string}'.");
        }
        
        $this->mysqli = $mysqli;
    }
    
    /**
     * Wrapper for MySQLi::query().
     *
     * @param string $query
     *
     */
    public function query($query)
    {
        $query_start_time = microtime(true);
        $result = $this->mysqli->query($query);
        $query_end_time = microtime(true);
        $failed = ($result === false);
        
        if ($failed && $this->throw_exception_when_query_fails)
        {
            throw new \RuntimeException("MySQLi MainWrapper query execution error. Error number: '{$this->mysqli->errno}', Error message: '{$this->mysqli->error}', Query: '{$query}'");
        }
        
        $this->logQueryHistory($query, !$failed, $query_end_time - $query_start_time);
        return $result;
    }
    
    /**
     * Create statement
     * 
     * 
     * 
     */
    public function createStatement($statement_string_with_placeholders)
    {
        return new StatementWrapper($this->mysqli, $statement_string_with_placeholders);
    }
    
    /**
     * Quickly prepares and execute a statement.
     *
     * Statement is discarded after the result set is gathered. Please
     * note that this function will loop through the results and store
     * them to an array before returning. If you are executing a statement
     * that returns huge result set, consider creating a statement with
     * MySQLiWrapper::createStatement() instead.
     *
     * @param string $statement_string_with_placeholders
     * @param array $params Parameters to be bind
     *
     */
    public function prepareAndExecuteStatement($statement_string_with_placeholders, array $params = array())
    {
        $statement = new StatementWrapper($this->mysqli, $statement_string_with_placeholders);
        $statement->execute($params);
        $statement->getAll();
    }
    
    /**
     * Sets 
     *
     */
    public function throwExceptionWhenQueryFails($bool)
    {
        $this->throw_exception_when_query_fails = (bool) $bool;
    }
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    public function getLastQueryExecutionTime()
    {
        return $this->query_history[count($this->query_history)-1]['time'];
    }
    
    // ---------------------------------------------------------------
    
    /**
     * Defies imagination, extends boundaries and saves the world ...all before breakfast!
     *
     */
    protected function logQueryHistory($query, $success, $time)
    {
        $history['query'] = $query;
        $history['success'] = $success;
        $history['time'] = $time;
        $this->query_history[] = $history;
    }
}