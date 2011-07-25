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
 * Wrapper for \MySQLi_STMT
 * 
// ---------------------------------------------------------------
 * Creates a \MySQLi_STMT object and acts as its wrapper, allowing you to use
 * prepared statements without dealing with parameter and result row binding.
 * It also allows you to use named placeholder, which makes reading and editing
 * prepared statements much easier when your query has more than 10 parameters.
 *
 * Placeholders are marked with colon (:) character at the beginning and
 * detected with this regular expression:
 *
 * <code>
 * :[a-zA-Z0-9_:]+
 * </code>
 *
 * Example of allowed placeholders:
 *
 * <code>
 * :placeholder
 * :place_holder
 * ::Placeholder
 * :123
 * </code>
 *
 * At object construction, placeholders will be replaced with '?' and used
 * to construct an instance of \MySQLi_STMT:
 *
 * <code>
 * $mysqli = new MySQLi('localhost', 'user', 'pwd', 'testdb');
 * $statement = new Carrot\Database\MySQLi\StatementWrapper($mysqli,
 *     'SELECT
 *         id, name, balance
 *      FROM
 *         accounts
 *      WHERE
 *         name LIKE :name_like,
 *         balance > :balance_lower_limit'
 * );
 * </code>
 *
 * You can then execute by passing associative array as parameter. The class
 * will check for integers and floats and mark their type accordingly. Default
 * parameter type is string.
 *
 * <code>
 * $params = array
 * (
 *     ':name_like' => 'John%',
 *     ':balance_lower_limit' => 25000
 * );
 *
 * $statement->execute($params);
 * </code>
 *
 * If you need to mark a parameter as blob, use this method before execution:
 *
 * <code>
 * $statement->markParamAsBlob(':blob_param');
 * </code>
 *
 * Fetching results is done using a while loop, you can fetch as array, associative
 * array, or an object:
 *
 * <code>
 * while ($row = $statement->fetchObject())
 * {
 *     echo "ID: {$row->id}";
 * }
 * </code>
 *
 * Default behavior is to return false when execution fails. You can tell the
 * object to throw an exception when execution fails using this method:
 *
 * <code>
 * $statement->throwExceptionWhenExecutionFails(true);
 * </code>
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Database\MySQLi;

class StatementWrapper
{
    /**
     * @var string Statement string with placeholders, injected during construction.
     */
    protected $statement_string_with_placeholders;
    
    /**
     * @var string Processed statement string, used in constructing the \MySQLi_STMT object, placeholders replaced with '?'.
     */
    protected $statement_string;
    
    /**
     * @var \MySQLi_STMT Instance of \MySQLi_STMT, constructed using {@see $statement_string}.
     */
    protected $statement_object;
    
    /**
     * @var array List of placeholders with the colon (:) prefix, extracted from {@see $statement_string_with_placeholders}.
     */
    protected $placeholders;
    
    /**
     * @var array List of placeholders with 'blob' data type, set by the user - see {@see }.
     */
    protected $blob_params = array();
    
    /**
     * @var mixed Contains the result of \MySQLi_STMT::result_metadata() call.
     */
    protected $result_metadata;
    
    /**
     * @var array Parameters used to execute the query.
     */
    protected $params;
    
    /**
     * @var string Parameter types in string, as per \MySQLi_STMT::bind_param() specification.
     */
    protected $param_types;
    
    /**
     * @var array Result row, filled with new values every time a new row is fetched.
     */
    protected $result_row;
    
    /**
     * @var array Contains references to the {@see $params} property, used for binding in bind_param().
     */
    protected $references_params;
    
    /**
     * @var array Contains references to the {@see $result_row} property, used for binding in bind_result().
     */
    protected $references_result_row;
    
    /**
     * @var bool If set to true, any subsequent execution that fails/returns false will trigger an exception.
     */
    protected $throw_exception_when_execution_fails = false;
    
    /**
     * @var bool True if the statement has a result set, false otherwise.
     */
    protected $has_result_set;
    
    /**
     * @var bool True if result set has been buffered using \MySQLi_STMT::store_result(), false otherwise.
     */
    protected $result_is_buffered = false;
    
    /**
     * Constructs an instance of the StatementWrapper.
     * 
     * Assumes that the MySQLi instance is already connected. Statement
     * string sent must use placeholders. Placeholders are marked by the
     * colon character (:).
     *
     * <code>
     * $statement = new StatementWrapper($mysqli,
     *     'SELECT
     *         id, name, balance
     *     FROM
     *         accounts
     *     WHERE
     *         id LIKE :id,
     *         name LIKE :name,
     *         balance > :balance'
     * );
     * </code>
     *
     * The statement string from above code will be converted to this
     * query:
     *
     * <code>
     * SELECT
     *     id, name, balance
     * FROM
     *     accounts
     * WHERE
     *     id LIKE ?,
     *     name LIKE ?,
     *     balance > ?
     * </code>
     *
     * with the following placeholders:
     *
     * <code>
     * :id
     * :name
     * :balance
     * </code>
     * 
     * @see createParameterVariablesAndReferences()
     * @see createResultVariablesAndReferences()
     * @param MySQLi $mysqli Instance of MySQLi (must be already connected).
     * @param string $statement_string String containing the statement (must use placeholders).
     *
     */
    public function __construct(\MySQLi $mysqli, $statement_string_with_placeholders)
    {
        $this->statement_string_with_placeholders = $statement_string_with_placeholders;
        $this->placeholders = $this->extractPlaceholders($statement_string_with_placeholders);
        $this->statement_string = $this->replacePlaceholdersWithQuestionMarks($statement_string_with_placeholders);
        $this->statement_object = $mysqli->prepare($this->statement_string);
        
        if ($this->statement_object === false)
        {
            throw new \RuntimeException("StatementWrapper error, fails to prepare the statement. Error number: '{$mysqli->errno}', Error message: '{$mysqli->error}', Processed statement: '{$this->statement_string}', Original statement: '{$this->statement_string_with_placeholders}'.");
        }
        
        $this->result_metadata = $this->statement_object->result_metadata();
        $this->has_result_set = $this->hasResultSet();
        $this->createParameterVariablesAndReferences();
        $this->createResultVariablesAndReferences();
        $this->bindResult();
    }
    
    /**
     * Executes the statement.
     *
     * Pass the parameters as associative array. Previously used
     * parameters will be used if you don't pass parameter array.
     * You don't need to pass anything if your statement doesn't
     * need parameters. 
     *
     * <code>
     * $statement = new StatementWrapper($mysqli, 'INSERT INTO accounts (id, first_name) VALUES (:id, :first_name));
     * $statement->execute(array(':id' => 'AB12345', ':first_name' => 'John'));
     * </code>
     * 
     * Will throw RuntimeException if execution fails and
     * $throw_exception_when_execution_fails is true.
     * 
     * @throws RuntimeException
     * @see $throw_exception_when_execution_fails
     * @param array $params Optional. Parameters to use for execution, if left empty will use previously set parameters.
     * @return bool Returns true if statement executed successfully, false otherwise.
     *
     */
    public function execute(array $params = array())
    {
        if (!empty($params))
        {
            $this->setAndBindParameters($params);
        }
        
        $result = $this->statement_object->execute();
        
        if (!$result && $this->throw_exception_when_execution_fails)
        {
            throw new \RuntimeException("StatementWrapper execution error! Error #{$this->statement_object->errno}: '{$this->statement_object->error}', statement is '{$this->statement_string}'.");
        }
        
        // After each execution, you need to call \MySQLi_STMT::store_result() again.
        $this->result_is_buffered = false;
        
        return $result;
    }
    
    /**
     * Fetches the result as array using \MySQLi_STMT::fetch().
     * 
     * Calls to this method is ignored if the statement doesn't have 
     * result. Use while() loop to iterate the result set:
     *
     * <code>
     * while ($row = $statement->fetchArray())
     * {
     *     echo "ID: {$row[0]}, Name: {$row[1]}";
     * }
     * </code>
     *
     * @return mixed Result row as array. False if no more rows or failure in fetching.
     *
     */
    public function fetchArray()
    {   
        $result = $this->statement_object->fetch();
        
        if ($result === true)
        {
            $row = array();
            
            foreach ($this->result_row as $content)
            {
                $row[] = $content;
            }
            
            return $row;
        }
        
        return false;
    }
    
    /**
     * Fetches the result as associative array using \MySQLi_STMT::fetch().
     * 
     * Calls to this method is ignored if the statement doesn't have 
     * result. Use while() loop to iterate the result set:
     *
     * <code>
     * while ($row = $statement->fetchAssociativeArray())
     * {
     *     echo "ID: {$row['id']}, Name: {$row['name']}";
     * }
     * </code>
     *
     * @return mixed Result row as associative array. False if no more rows or failure in fetching.
     *
     */
    public function fetchAssociativeArray()
    {
        $result = $this->statement_object->fetch();
        
        if ($result === true)
        {
            $row = array();
            
            foreach ($this->result_row as $field_name => $content)
            {
                $row[$field_name] = $content;
            }
            
            return $row;
        }
        
        return false;
    }
    
    /**
     * Fetches the result as PHP standard object using \MySQLi_STMT::fetch().
     * 
     * Calls to this method is ignored if the statement doesn't have 
     * result. Use while() loop to iterate the result set:
     *
     * <code>
     * while ($row = $statement->fetchObject())
     * {
     *     echo "ID: {$row->id}, Name: {$row->name}";
     * }
     * </code>
     *
     * @return mixed Result row as PHP standard object. False if no more rows or failure in fetching.
     *
     */
    public function fetchObject()
    {
        $result = $this->statement_object->fetch();
        
        if ($result === true)
        {
            $row = array();
            
            foreach ($this->result_row as $field_name => $content)
            {
                $row[$field_name] = $content;
            }
            
            return (object) $row;
        }
        
        return false;
    }
    
    /**
     * Fetch the whole result set with each row as array.
     *
     * @return array Array containing all the result rows.
     *
     */
    public function fetchAllAsArray()
    {
        $result_set = array();
        
        while ($row = $this->fetchArray())
        {
            $result_set[] = $row;
        }
        
        return $result_set;
    }
    
    /**
     * Fetch the whole result set with each row as associative array.
     *
     * @return array Array containing all the result rows.
     *
     */
    public function fetchAllAsAssociativeArray()
    {
        $result_set = array();
        
        while ($row = $this->fetchAssociativeArray())
        {
            $result_set[] = $row;
        }
        
        return $result_set;
    }
    
    /**
     * Fetch the whole result set with each row as PHP standard object.
     *
     * @return array Array containing all the result rows.
     *
     */
    public function fetchAllAsObject()
    {
        $result_set = array();
        
        while ($row = $this->fetchObject())
        {
            $result_set[] = $row;
        }
        
        return $result_set;
    }
    
    /**
     * Mark parameter placeholder as 'blob' type.
     *
     * For each statement execution, parameters are automatically
     * assigned proper type by detecting the parameter variable type
     * using is_integer(), is_float(), and is_string(). Parameter type
     * defaults to string. If you have to send a blob parameter type,
     * use this method to mark the placeholder as such.
     *
     * <code>
     * $statement->markParamAsBlob(':blob_param');
     * </code>
     *
     * @see $blob_params
     * @param string $placeholder The placeholder you want to mark as blob, with colon (:).
     *
     */
    public function markParamAsBlob($placeholder)
    {
        if (!isset($this->placeholders[$placeholder]))
        {
            throw new \RuntimeException("StatementWrapper error in marking parameter as blob. Placeholder '{$placeholder}' is not defined.");
        }
        
        $this->blob_params[] = $placeholder;
    }
    
    /**
     * Tells the class to throw/not to throw exception when statement execution fails.
     *
     * Default behavior is to NOT throw exception when the query fails
     * and simply return false. This makes it easier for single statements,
     * however if you need to craft a transaction, you can tell this
     * class to throw exception if execution fails (for whatever reason).
     *
     * <code>
     * $statement->throwExceptionWhenExecutionFails(true);
     * </code>
     *
     * @param bool $bool Pass true to throw exceptions, false otherwise.
     *
     */
    public function throwExceptionWhenExecutionFails($bool)
    {
        $this->throw_exception_when_execution_fails = $bool;
    }
    
    /**
     * See if the result set is buffered or not.
     *
     * The result set is buffered if \MySQLi_STMT::store_result() is
     * called after each statement execution. The wrapper notes this
     * by setting $result_is_buffered property to true every time
     * \MySQLi_STMT::store_result() is called.
     *
     * The wrapper does not buffer the result by default, following
     * \MySQLi_STMT standard behavior.
     *
     * If the result set is not buffered, \MySQLi_STMT->num_rows will
     * not return a valid response.
     *
     * @return bool True if buffered, false otherwise.
     *
     */
    public function resultIsBuffered()
    {
        return $this->result_is_buffered;
    }
    
    /**
     * Returns the result metadata.
     *
     * This method does not call \MySQLi_STMT::result_metadata(),
     * it simply returns a saved value since \MySQLi_STMT::result_metadata()
     * is already called in construction.
     *
     * @return mixed Instance of \MySQLi_Result or false if there isn't a result.
     *
     */
    public function getResultMetadata()
    {
        return $this->result_metadata;
    }
    
    /**
     * Destroys this object.
     *
     * Calls \MySQLi_STMT::close() for safety.
     *
     */
    public function __destruct()
    {
        $this->result_is_buffered = false;
        $this->statement_object->close();
    }
    
    /**
     * Wrapper for \MySQLi_STMT->affected_rows.
     * 
     * @return mixed -1 indicates query error.
     *
     */
    public function getAffectedRows()
    {
        return $this->statement_object->affected_rows;
    }
    
    /**
     * Wrapper for \MySQLi_STMT::attr_get().
     * 
     * @param int $attr The attribute you want to get.
     * @return mixed False if the attribute is not found, otherwise return value of the attribute.
     *
     */
    public function getAttr($attr)
    {
        return $this->statement_object->attr_get($attr);
    }
    
    /**
     * Wrapper for \MySQLi_STMT::attr_set().
     *
     * @param int $attr The attribute you want to set.
     * @param int $mode The value to assign to the attribute.
     *
     */
    public function setAttr($attr, $mode)
    {
        $this->statement_object->attr_set($attr, $mode);
    }
    
    /**
     * Wrapper for \MySQLi_STMT::data_seek().
     *
     * @param int $offset
     *
     */
    public function dataSeek($offset)
    {
        $this->statement_object->data_seek($offset);
    }
    
    /**
     * Wrapper for \MySQLi_STMT->errno.
     *
     * @return int Error number for the last execution.
     *
     */
    public function getErrorNo()
    {
        return $this->statement_object->errno;
    }
    
    /**
     * Wrapper for \MySQLi_STMT->error.
     * 
     * @return string Error message for last execution.
     *
     */
    public function getErrorMessage()
    {
        return $this->statement_object->error;
    }
    
    /**
     * Wrapper for \MySQLi_STMT->field_count.
     *
     * @return int Number of fields in the given statement.
     *
     */
    public function getFieldCount()
    {
        return $this->statement_object->field_count;
    }
    
    /**
     * Wrapper for \MySQLi_STMT::free_result().
     *
     * This method also notes that result buffer has been cleared by
     * setting $result_is_buffered property to false.
     *
     * When you run a prepared statement that returns a result set, it
     * locks the connection unless you free_result() or store_result().
     *
     */
    public function freeResult()
    {
        $this->statement_object->free_result();
        $this->result_is_buffered = false;
    }
    
    /**
     * Wrapper for \MySQLi_STMT::get_warnings().
     *
     * @return mixed
     *
     */
    public function getWarnings()
    {
        return $this->statement_object->get_warnings();
    }
    
    /**
     * Wrapper for \MySQLi_STMT->insert_id.
     *
     * @return int The ID generated from previous INSERT operation.
     *
     */
    public function getInsertID()
    {
        return $this->statement_object->insert_id;
    }
    
    /**
     * Wrapper for \MySQLi_STMT->num_rows.
     *
     * This method does not return invalid row count, it returns false
     * if result set is not buffered.
     * 
     * @return mixed Number of rows if result is buffered, false if result set is not buffered.
     *
     */
    public function getNumRows()
    {
        if ($this->result_is_buffered)
        {
            return $this->statement_object->num_rows;
        }
        
        return false;
    }
    
    /**
     * Wrapper for \MySQLi_STMT->param_count.
     *
     * @return int $param_count Number of parameters in the statement.
     *
     */
    public function getParamCount()
    {
        return $this->statement_object->param_count;
    }
    
    /**
     * Wrapper for \MySQLi_STMT::reset().
     *
     * \MySQLi_STMT::reset() does not unbind parameter. After you reset,
     * you can safely execute it again even if the query has parameters.
     *
     * @return bool True on success, false on failure.
     *
     */
    public function reset()
    {
        return $this->statement_object->reset();
    }
    
    /**
     * Wrapper for \MySQLi_STMT->sqlstate.
     *
     * @return string SQLSTATE code from previous statement operation.
     *
     */
    public function getSQLState()
    {
        return $this->statement_object->sqlstate;
    }
    
    /**
     * Wrapper for \MySQLi_STMT::store_result().
     *
     * This method also sets $result_is_buffered property to true,
     * allowing you getNumRows() method to return valid value. This
     * method must be called *after* execution.
     *
     * @return bool True on success, false on failure.
     *
     */
    public function storeResult()
    {
        $this->result_is_buffered = $this->statement_object->store_result();
        return $this->result_is_buffered;
    }
    
    /**
     * Extracts placeholder names from original statement string.
     *
     * Placeholder is defined with this regular expression:
     *
     * <code>
     * :[a-zA-Z0-9_:]+
     * </code>
     *
     * We use the colon character to follow PDO's placeholder behavior.
     * This should make usage of this class familiar enough for most
     * people.
     *
     * <code>
     * :placeholder
     * :123placeholder
     * :_place_holder
     * ::placeholder
     * :place:holder
     * </code>
     *
     * @param string $statement_string_with_placeholders
     * @return array Array that contains placeholder names.
     *
     */
    protected function extractPlaceholders($statement_string_with_placeholders)
    {
        preg_match_all('/:[a-zA-Z0-9_:]+/', $statement_string_with_placeholders, $matches);
        
        if (isset($matches[0]) && is_array($matches[0]))
        {
            return $matches[0];
        }
        
        return array();
    }
    
    /**
     * Replaces placeholders (:string) with '?'.
     *
     * This in effect creates a statement string that we can use it
     * to instantiate a MySQLi statement object. It replaces this
     * pattern:
     *
     * <code>
     * :[a-zA-Z0-9_:]+
     * </code>
     *
     * with question mark ('?'). Returns empty array if no placeholder
     * is found.
     *
     * @param string $statement_string_with_placeholders
     * @return string Statement string safe to use as \MySQLi_STMT instantiation argument.
     *
     */
    protected function replacePlaceholdersWithQuestionMarks($statement_string_with_placeholders)
    {
        return preg_replace('/:[a-zA-Z0-9_:]+/', '?', $statement_string_with_placeholders);
    }
    
    /**
     * Creates parameter array to store parameters and a set of references that refers to it.
     * 
     * \MySQLi_STMT::bind_param() requires the arguments to be
     * references, so not only we have to create parameter array
     * to store parameters set by the user, we also have to create
     * references to them to be used when binding parameters.
     * 
     * @see $params
     * @see $references_params
     * @see __construct()
     * 
     */
    protected function createParameterVariablesAndReferences()
    {
        $placeholder_count = count($this->placeholders);
        
        if ($this->statement_object->param_count != $placeholder_count)
        {
            throw new \RuntimeException("StatementWrapper error, fails to prepare the statement. Parameter count ({$this->statement_object->param_count}) and placeholder count ({$placeholder_count}) does not match.");
        }
        
        $this->references_params['types'] = &$this->param_types;
        
        foreach ($this->placeholders as $placeholder)
        {
            $this->params[$placeholder] = null;
            $this->references_params[$placeholder] = &$this->params[$placeholder];
        }
    }
    
    /**
     * Creates array to store a fetched result row and a set of references that refers to it.
     *
     * \MySQLi_STMT::bind_result() requires the arguments to be
     * references, so not only we have to create a result row
     * variables to store fetched row variables, we also have to
     * create references to them to be used when binding result.
     *
     * @see $result_row
     * @see $references_result_row
     * @see __construct()
     *
     */
    protected function createResultVariablesAndReferences()
    {
        if ($this->has_result_set)
        {
            foreach ($this->result_metadata->fetch_fields() as $field)
            {
                $this->result_row[$field->name] = null;
                $this->references_result_row[$field->name] = &$this->result_row[$field->name];
            }
        }
    }
    
    /**
     * Binds result row references using \MySQLi_STMT::bind_result().
     * 
     * We only need to bind the result once, hence this method is called
     * only at the constructor.
     *
     * @see $result_row
     * @see $references_result_row
     * @see __construct()
     *
     */
    protected function bindResult()
    {
        if ($this->has_result_set)
        {
            call_user_func_array(array($this->statement_object, 'bind_result'), $this->references_result_row);
        }
    }
    
    /**
     * Sets and binds parameters for the next execution.
     *
     * Will throw RuntimeException if the parameter array count doesn't
     * match the parameter/placeholder count.
     *
     * Will throw RuntimeException if the parameter index doesn't contain
     * all placeholders as its indexes.
     * 
     * @throws RuntimeException
     * @see execute()
     * @param array $params Complete parameter array, indexed with placeholders.
     *
     */
    protected function setAndBindParameters(array $params)
    {
        // Ignore method call if we don't have parameters to process
        if ($this->statement_object->param_count <= 0)
        {
            return;
        }
        
        $user_param_count = count($params);
        $param_type_string = '';
        
        if ($this->statement_object->param_count != $user_param_count)
        {
            throw new \RuntimeException("StatementWrapper error when setting and binding parameters. Argument count ({$user_param_count}) doesn't match needed parameter count ({$this->statement_object->param_count}).");
        }
        
        foreach ($this->params as $placeholder => $param)
        {
            if (!isset($params[$placeholder]))
            {
                throw new \RuntimeException("StatementWrapper error when setting and binding parameters. Required parameter '{$placeholder}' is not defined when trying to set parameter.");
            }
            
            $this->params[$placeholder] = $params[$placeholder];
        }
        
        $this->createParamTypeString();
        $this->bindParam();
    }
    
    /**
     * Fills parameter types string to the $references_param property.
     * 
     * \MySQLi_STMT::bind_param() requires us to specify parameter types
     * when binding. Allowed parameter types are (as per 5.3.6):
     *
     * <code>
     * i - integer
     * d - double
     * s - string
     * b - blob (will be sent in packets)
     * </code>
     *
     * This method detects if the parameter is integer or float (double)
     * and defaults to string. To mark a parameter as blob, use class
     * method markParamAsBlob().
     *
     * @see $references_params
     * @see setAndBindParameters()
     * @see markParamAsBlob()
     *
     */
    protected function createParamTypeString()
    {
        $this->references_params['types'] = '';
        
        foreach ($this->params as $placeholder => $param)
        {
            if (in_array($placeholder, $this->blob_params))
            {
                $this->references_params['types'] .= 'b';
            }
            else if (is_integer($param))
            {
                $this->references_params['types'] .= 'i';
            }
            else if (is_float($param))
            {
                $this->references_params['types'] .= 'd';
            }
            else
            {
                $this->references_params['types'] .= 's';
            }
        }
    }
    
    /**
     * Binds parameter references array using \MySQLi_STMT::bind_param().
     *
     * This method is called each time the user provides new arguments.
     * Assumes that parameter types string has already been generated.
     *
     * @see $references_params
     * @see createParameterVariablesAndReferences()
     *
     */
    protected function bindParam()
    {
        call_user_func_array(array($this->statement_object, 'bind_param'), $this->references_params);
    }
    
    /**
     * Checks if the statement has a result set or not.
     *
     * If the statement has a result set, \MySQLi_STMT::result_metadata() will
     * return a \MySQLi_Result object. If the statement has no result set it
     * will return false.
     * 
     * @return bool True if the statement has a result set, false otherwise.
     *
     */
    protected function hasResultSet()
    {
        return (is_object($this->result_metadata) && is_a($this->result_metadata, '\MySQLi_Result'));
    }
}