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
 * Wraps \MySQLi object and contains the factory method for 
 * \Carrot\Database\MySQLi\StatementWrapper. To instantiate, you
 * need an instance of \MySQLi that is connected.
 *
 * <code>
 * $mysqli = new \MySQLi('localhost', 'user', 'pwd', 'testdb');
 * $db = new \Carrot\Database\MySQLi\MainWrapper($mysqli);
 * </code>
 *
 * Query the usual way:
 *
 * <code>
 * $result = $db->query('SELECT * FROM accounts');
 *
 * while ($row = $result->fetch_assoc())
 * {
 *     var_dump($row);
 * }
 * </code>
 *
 * Create and run prepared statements {@see \Carrot\Database\MySQLi\StatementWrapper}:
 *
 * <code>
 * $stmt = $db->createStatement('SELECT * FROM accounts WHERE id = :id');
 * $stmt->execute(array(':id' => 'AB2535'));
 * </code>
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
     * Wrapper for \MySQLi::query().
     * 
     * Other than calling \MySQLi::query(), it also logs the query
     * history, including query run time.
     *
     * @see logQueryHistory()
     * @param string $query Query string to run.
     * @return mixed Returns what \MySQLi::query() returns.
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
            throw new \RuntimeException("MySQLi MainWrapper query execution error. Error number: '{$this->mysqli->errno}', Error message: '{$this->mysqli->error}', Query: '{$query}'.");
        }
        
        $this->logQueryHistory($query, !$failed, $query_end_time - $query_start_time);
        return $result;
    }
    
    /**
     * Create an instance of \Carrot\Database\MySQLi\StatementWrapper.
     *
     * Acts as the factory method for \Carrot\Database\MySQLi\StatementWrapper.
     * 
     * @see \Carrot\Database\MySQLi\StatementWrapper
     * @param string $statement_string_with_placeholders
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
     * createStatement() instead.
     * 
     * This method will return the result rows formatted as arrays as
     * its default behavior. You can tell this method to return the rows
     * formatted by passing the third optional parameter.
     *
     * <code>
     * $count = $mysqli_wrapper->quickExecuteStatement
     * (
     *     'SELECT COUNT(*) AS `count` FROM accounts WHERE id = :blah',
     *     array(':id' => 'AB12345'),
     *     'object'
     * );
     *
     * echo $count[0]->count;
     * </code>
     *
     * @param string $statement_string_with_placeholders
     * @param array $params Statement parameters in associative array.
     * @param string $row_type Optional. Determines how to format each result row, use either 'assoc', 'object', or 'array'. Defaults to array.
     * @return array Array that contains the whole result set.
     *
     */
    public function quickExecuteStatement($statement_string_with_placeholders, array $params = array(), $row_type = 'array')
    {
        $statement = new StatementWrapper($this->mysqli, $statement_string_with_placeholders);
        $statement->execute($params);
        
        switch (strtolower($row_type))
        {
            case 'assoc':
                $result_set = $statement->fetchAllAsAssociativeArray();
            break;
            case 'object':
                $result_set = $statement->fetchAllAsObject();
            break;
            default:
                $result_set = $statement->fetchAllAsArray();
            break;
        }
        
        return $result_set;
    }
    
    /**
     * Tells the class to throw/not to throw exception when query fails.
     *
     * Default behavior is NOT to throw exceptions when query fails.
     * Use this method to tell the class whether or not to throw
     * exception if query fails.
     *
     */
    public function throwExceptionWhenQueryFails($bool)
    {
        $this->throw_exception_when_query_fails = (bool) $bool;
    }
    
    /**
     * Get last query execution time.
     * 
     * @return float Last query execution time.
     *
     */
    public function getLastQueryExecutionTime()
    {
        return $this->query_history[count($this->query_history)-1]['time'];
    }
    
    /**
     * Get the complete query history (ran on this object).
     *
     * This is useful if you wanted to log query information. The
     * format of the query history array is as follows:
     *
     * <code>
     * $query_history = array
     * (
     *     0 => array
     *     (
     *         'query' => 'SELECT * FROM accounts',
     *         'success' => true
     *         'time' => 0.0556
     *     ),
     *     1 => array
     *     (
     *         'query' => 'START TRANSACTION',
     *         'success' => true,
     *         'time' => 0.0236
     *     )
     * );
     * </code>
     * 
     * @return array Query history array.
     * 
     */
    public function getQueryHistory()
    {
        return $this->query_history;
    }
    
    // ---------------------------------------------------------------
    
    /**
     * Stores information about a query ran by this object.
     *
     * @param string $query
     * @param bool $success True if query is successful, false otherwise.
     * @param float $time Time elapsed to complete the query.
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