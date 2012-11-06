<?php

namespace Carrot\MySQLi;

use MySQLi as MySQLi_Parent,
    MySQLi_Result,
    MySQLi_STMT,
    RuntimeException,
    Carrot\MySQLi\Exception\StatementException,
    Carrot\MySQLi\Exception\DuplicateKeyException;

/**
 * MySQLi Statement Wrapper
 *
 * This class extends MySQLi_STMT and allows you to execute and
 * retrieve row results from a real prepared statement without
 * binding parameters or result row. It also allows you to use
 * named placeholders, which makes reading and editing prepared
 * statements much easier.
 *
 * Placeholders are marked with colon (:) character at the
 * beginning and detected with this regular expression:
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
 * To construct, you can use Carrot's MySQLi wrapper in the same
 * namespace to build this statement:
 *
 * <code>
 * $mysqli = new Presensi\Model\Driver\MySQLi(
 *     'hostname',
 *     'username',
 *     'password',
 *     'database'
 * );
 *
 * $statement = $mysqli->buildStatement(
 *     'SELECT
 *         id, name, balance
 *     FROM
 *         accounts
 *     WHERE
 *         id LIKE :id AND
 *         name LIKE :name AND
 *         balance > :balance'
 * );
 * </code>
 *
 * You can then run the prepared statement as easy as this:
 *
 * <code>
 * $statement->execute(array(
 *     ':id' => $id,
 *     ':name' => $name,
 *     ':balance' => $balance
 * ));
 * </code>
 *
 * If there is a result set, fetch it with fetchArray(),
 * fetchObject() or fetchAssociativeArray():
 *
 * <code>
 * while ($row = $statement->fetchAssociativeArray())
 * {
 *     echo "ID: {$row['id']}, Name: {$row['name']}";
 * }
 * </code>
 *
 * You don't need to specify the variable types as this class
 * will generate the variable types when executing based on the
 * actual variable type. However, if you need to send a variable
 * with 'blob' as the data type, you need to set it explicitly:
 *
 * <code>
 * $statement->markParamAsBlob(':blobParamName');
 * </code>
 *
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */
class Statement extends MySQLi_STMT
{
    /**
     * @var string Processed statement string, used in constructing
     *      the MySQLi_STMT object, placeholders replaced with '?'.
     */
    protected $query;

    /**
     * @var array List of placeholders with the colon (:) prefix,
     *      extracted from {@see $statementStringWithPlaceholders}.
     */
    protected $placeholders;

    /**
     * @var array List of placeholders with 'blob' data type, set by
     *      the user - see {@see markParamAsBlob()}.
     */
    protected $blobParams;

    /**
     * @var bool TRUE if the statement has a result set, FALSE
     *      otherwise.
     */
    protected $hasResultSet;

    /**
     * @var string Parameter types in string, as per
     *      {@see MySQLi_STMT::bind_param()} specification.
     */
    protected $paramTypes;

    /**
     * @var array Parameters used to execute the query.
     */
    protected $params;

    /**
     * @var array Contains references to the {@see $params} property,
     *      used for binding using parent::bind_param().
     */
    protected $paramsReferences;

    /**
     * @var array Result row, filled with new values every time a new
     *      row is fetched.
     */
    protected $resultRow;

    /**
     * @var array Contains references to the {@see $resultRow}
     *      property, used for binding using parent::bind_result().
     */
    protected $resultRowReferences;

    /**
     * Constructor.
     *
     * To construct this class, specify the MySQLi class/link, the
     * query, and an array of placeholders:
     *
     * <code>
     * $statement = new StatementWrapper(
     *     $mysqli,
     *     'INSERT INTO accounts (id, firstName) VALUES (?, ?)',
     *     array(
     *         ':id',
     *         ':firstName'
     *     )
     * );
     * </code>
     *
     * For easier object construction, use Carrot's MySQLi wrapper
     * class to create this object, as in:
     *
     * <code>
     * $statement = $mysqli->buildStatement(
     *     'SELECT
     *         id, name, balance
     *     FROM
     *         accounts
     *     WHERE
     *         id LIKE :id AND
     *         name LIKE :name AND
     *         balance > :balance'
     * );
     * </code>
     *
     * Thanks to the user notes at PHP documentation page, I found out
     * the prototype of the constructor of MySQLi_STMT, which is
     * __construct($link, $query).
     *
     * @throws RuntimeException If an error occured when creating the
     *         statement object.
     * @param MySQLi $mysqli The MySQLi link.
     * @param string $query The query to run.
     * @param array $placeholders The list of placeholders.
     *
     */
    public function __construct(MySQLi_Parent $mysqli, $query, array $placeholders = array())
    {
        parent::__construct($mysqli, $query);

        if (isset($this->error) AND !empty($this->error))
        {
            throw new RuntimeException("Statement error in instantiating. Error number #{$this->errno} with the message '{$this->error}', query after transformation is '$query'.");
        }

        $this->query = $query;
        $this->placeholders = $placeholders;
        $this->blobParams = array();
        $this->validatePlaceholders();
        $resultMetadata = $this->result_metadata();
        $this->hasResultSet = $this->hasResultSet($resultMetadata);
        $this->createParametersArrayAndReferences();
        $this->createResultRowArrayAndReferences($resultMetadata);
        $this->bindResultRowArrayReferences();
    }

    /**
     * Executes the query with the given parameters.
     *
     * Pass the parameters as associative array. Previously used
     * parameters will be used if you don't pass parameter array.
     * You don't need to pass anything if your statement doesn't
     * need parameters.
     *
     * <code>
     * $statement = new StatementWrapper(
     *     $mysqli,
     *     'INSERT INTO accounts (id, firstName) VALUES (?, ?)',
     *     array(
     *         ':id',
     *         ':firstName'
     *     )
     * );
     *
     * $statement->execute(array(':id' => 'AB12345', ':firstName' => 'John'));
     * </code>
     *
     * @param array $params The parameters to be used in executing.
     * @return bool TRUE if statement executed successfully, FALSE
     *         otherwise.
     *
     */
    public function execute(array $params = array())
    {
        if (!empty($params))
        {
            $this->setAndBindParameters($params);
        }

        $result = parent::execute();

        if (!$result)
        {
            $this->throwException();
        }

        return $result;
    }

    /**
     * Fetches the result as array using MySQLi_STMT::fetch().
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
     * @return array|FALSE|NULL Result row as array. FALSE if an
     *         error has occurred, NULL if no more data exists.
     *
     */
    public function fetchArray()
    {
        $result = $this->fetch();

        if ($result === TRUE)
        {
            $row = array();

            foreach ($this->resultRow as $content)
            {
                $row[] = $content;
            }

            return $row;
        }

        return $result;
    }

    /**
     * Fetch all result rows as an array using {@see fetchArray()}.
     *
     * Useful if the user wanted to get the whole result at once
     * without further processing. Simply loops the call to
     * {@see fetchArray()} until there is no more result row to add.
     *
     * @return array Array containing the result rows in numerical
     *         arrays. Empty arrays if no rows/error occurred.
     *
     */
    public function fetchAll()
    {
        $allRows = array();

        while ($row = $this->fetchArray())
        {
            $allRows[] = $row;
        }

        return $allRows;
    }

    /**
     * Fetches the result as associative array using MySQLi_STMT::fetch().
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
     * @return array|FALSE|NULL Result row as array. FALSE if an
     *         error has occurred, NULL if no more data exists.
     *
     */
    public function fetchAssociativeArray()
    {
        $result = $this->fetch();

        if ($result === TRUE)
        {
            $row = array();

            foreach ($this->resultRow as $fieldName => $content)
            {
                $row[$fieldName] = $content;
            }

            return $row;
        }

        return $result;
    }

    /**
     * Fetch all result rows as an associative array using
     * {@see fetchAssociativeArray()}.
     *
     * Useful if the user wanted to get the whole result at once
     * without further processing. Simply loops the call to
     * {@see fetchAssociativeArray()} until there is no more result
     * row to add.
     *
     * @return array Array containing the result rows in associative
     *         arrays. Empty array if no rows/error occurred.
     *
     */
    public function fetchAllAssociative()
    {
        $allRows = array();

        while ($row = $this->fetchAssociativeArray())
        {
            $allRows[] = $row;
        }

        return $allRows;
    }

    /**
     * Fetches the result as PHP standard object using MySQLi_STMT::fetch().
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
     * @return array|FALSE|NULL Result row as array. FALSE if an
     *         error has occurred, NULL if no more data exists.
     *
     */
    public function fetchObject()
    {
        $result = $this->fetch();

        if ($result === TRUE)
        {
            $row = array();

            foreach ($this->resultRow as $fieldName => $content)
            {
                $row[$fieldName] = $content;
            }

            return (object) $row;
        }

        return $result;
    }

    /**
     * Fetch all result rows as a PHP standard object using
     * {@see fetchObject()}.
     *
     * Useful if the user wanted to get the whole result at once
     * without further processing. Simply loops the call to
     * {@see fetchObject()} until there is no more result row to add.
     *
     * @return mixed Array containing the result rows in PHP standard
     *         objects. Empty array if no rows/error occurred.
     *
     */
    public function fetchAllObject()
    {
        $allRows = array();

        while ($row = $this->fetchObject())
        {
            $allRows[] = $row;
        }

        return $allRows;
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
     * $statement->markParamAsBlob(':blobParam');
     * </code>
     *
     * @see $blobParams
     * @param string $placeholder The placeholder you want to mark as
     *        blob, with colon (:) prefix.
     *
     */
    public function markParamAsBlob($placeholder)
    {
        if (!isset($this->placeholders[$placeholder]))
        {
            throw new RuntimeException("StatementWrapper error in marking parameter as blob. Placeholder '{$placeholder}' is not defined.");
        }

        $this->blob_params[] = $placeholder;
    }

    /**
     * Validates the placeholders array.
     *
     * Makes sure that the placeholders array count and the parameter
     * count of the MySQLi_STMT object is the same.
     *
     * @see __construct()
     *
     */
    protected function validatePlaceholders()
    {
        $placeholderCount = count($this->placeholders);

        if ($this->param_count != $placeholderCount)
        {
            throw new RuntimeException("Statement error in constructing, fails to prepare the statement. Parameter count ({$this->param_count}) and placeholder count ({$placeholderCount}) does not match.");
        }
    }

    /**
     * Checks if the statement has a result set or not.
     *
     * If the statement has a result set,
     * MySQLi_STMT::result_metadata() will return a MySQLi_Result
     * object. If the statement has no result set it will return FALSE.
     *
     * @see __construct()
     * @return bool TRUE if the statement has a result set, FALSE
     *         otherwise.
     *
     */
    protected function hasResultSet($resultMetadata)
    {
        return (is_object($resultMetadata) AND is_a($resultMetadata, 'MySQLi_Result'));
    }

    /**
     * Creates parameter array to store parameters and a set of
     * references that refers to it.
     *
     * MySQLi_STMT::bind_param() requires the arguments to be
     * references, so not only we have to create parameter array to
     * store parameters set by the user, we also have to create
     * references to them to be used when binding parameters.
     *
     * @see $params
     * @see $paramsReferences
     * @see __construct()
     *
     */
    protected function createParametersArrayAndReferences()
    {
        $this->paramsReferences = array();
        $this->paramsReferences['types'] = &$this->paramTypes;

        foreach ($this->placeholders as $placeholder)
        {
            $this->params[$placeholder] = NULL;
            $this->paramsReferences[$placeholder] = &$this->params[$placeholder];
        }
    }

    /**
     * Creates array to store a fetched result row and a set of
     * references that refers to it.
     *
     * MySQLi_STMT::bind_result() requires the arguments to be
     * references, so not only we have to create a result row
     * variables to store fetched row variables, we also have to
     * create references to them to be used when binding result.
     *
     * @see $resultRow
     * @see $resultRowReferences
     * @see __construct()
     *
     */
    protected function createResultRowArrayAndReferences($resultMetadata)
    {
        if ($this->hasResultSet)
        {
            foreach ($resultMetadata->fetch_fields() as $field)
            {
                $this->resultRow[$field->name] = NULL;
                $this->resultRowReferences[$field->name] = &$this->resultRow[$field->name];
            }
        }
    }

    /**
     * Binds the $resultRowReferences class property with
     * bind_result().
     *
     * Will not perform the binding if class property $hasResultSet is
     * FALSE. We can immediately bind result references after creating
     * it in constructor because once a statement has been made, the
     * result set would not change.
     *
     * @see __construct()
     * @see $resultRow
     * @see $resultRowReferences
     * @see $hasResultSet
     *
     */
    protected function bindResultRowArrayReferences()
    {
        if ($this->hasResultSet)
        {
            call_user_func_array(array('parent', 'bind_result'), $this->resultRowReferences);
        }
    }

    /**
     * Fill the parameters array and binds parameters for the next
     * execution.
     *
     * Will throw RuntimeException if the user parameters given
     * doesn't contain the placeholders needed.
     *
     * @see execute()
     * @throws RuntimeException If the user parameters given doesn't
     *         contain the placeholders needed.
     * @param array $params Complete parameter array, indexed with
     *        placeholders.
     *
     */
    protected function setAndBindParameters(array $userParams)
    {
        // Ignore method call if we don't have parameters to process
        if ($this->param_count <= 0)
        {
            return;
        }

        foreach ($this->params as $placeholder => $param)
        {
            if (!array_key_exists($placeholder, $userParams))
            {
                throw new RuntimeException("Statement error when setting and binding parameters. Required parameter '{$placeholder}' is not defined when trying to set parameter.");
            }

            $this->params[$placeholder] = $userParams[$placeholder];
        }

        $this->createParamTypeString();
        call_user_func_array(array('parent', 'bind_param'), $this->paramsReferences);
    }

    /**
     * Fills parameter types string to the $paramsReferences
     * property.
     *
     * MySQLi_STMT::bind_param() requires us to specify parameter
     * types when binding. Allowed parameter types are
     * (as per 5.3.6):
     *
     * <code>
     * i - integer
     * d - double
     * s - string
     * b - blob (will be sent in packets)
     * </code>
     *
     * This method detects if the parameter is integer or float
     * (double) and defaults to string. To mark a parameter as blob,
     * use class method markParamAsBlob().
     *
     * @see $paramsReferences
     * @see setAndBindParameters()
     * @see markParamAsBlob()
     *
     */
    protected function createParamTypeString()
    {
        $this->paramsReferences['types'] = '';

        foreach ($this->params as $placeholder => $param)
        {
            if (in_array($placeholder, $this->blobParams))
            {
                $this->paramsReferences['types'] .= 'b';
            }
            else if (is_integer($param))
            {
                $this->paramsReferences['types'] .= 'i';
            }
            else if (is_float($param))
            {
                $this->paramsReferences['types'] .= 'd';
            }
            else
            {
                $this->paramsReferences['types'] .= 's';
            }
        }
    }

    /**
     * Metode ini akan melempar kelas exception yang berbeda
     * tergantung dengan kode error MySQL.
     *
     * @see execute()
     *
     */
    protected function throwException()
    {
        $message = 'Statement execution error. Error code: #'. $this->errno
                 . '. Error message: '. $this->error;

        if ($this->errno == '1062')
        {
            throw new DuplicateKeyException(
                $message,
                $this->errno,
                $this->error,
                $this->sqlstate,
                $this->query,
                $this->params
            );
        }

        throw new StatementException(
            $message,
            $this->errno,
            $this->error,
            $this->sqlstate,
            $this->query,
            $this->params
        );
    }
}