<?php

/**
 * TEMPORARY FILE FOR MUCKING AROUND
 *
 * Yea I know this is dirty, it's temporary!
 *
 */

require 'autoload.php';

/**
 * If you wanted to store result set, you must call store_result()
 * after each execution.
 *
 */

$mysqli = new MySQLi('localhost', 'root', 'root', 'test2');

$statement = new Carrot\Database\MySQLi\StatementWrapper($mysqli, 'SELECT * FROM account WHERE id = #id');
$statement->throwExceptionWhenExecutionFails(true);
$result = $statement->execute(array('#id' => 1));
$statement->reset();
$statement->execute();

while ($row = $statement->fetchObject())
{
    echo '<pre>', var_dump($row), '</pre>';
    //echo '<pre>', var_dump($statement), '</pre>';
}

exit;

/**
 * MySQLi_STMT::fetch() will return:
 *
 *   1. True, if a row is fetched.
 *   2. False, if the statement isn't executed yet/Error occured.
 *   3. Null, if there is no more row to fetch.
 *
 * MySQLi_STMT::result_metadata() can be called before the
 * statement is executed, it returns:
 *
 *   1. Instance of MySQLi_Result, so you can
 *      poke around with functions.
 *   2. False, if the statement doesn't have
 *      any result set.
 *
 */

$stmt = $mysqli->prepare('SELECT title, body, `int` FROM recipes WHERE title LIKE ? OR body LIKE ?');
$title = '%a%';
$stmt->bind_param('ss', $title, $body);
$stmt->bind_result($res_title, $res_body, $res_int);
$stmt->execute();
$stmt->fetch();
//$stmt->fetch();
//$metadata = $stmt->result_metadata();

echo '<pre>', var_dump($res_title, $res_body), '</pre>';

exit;

$mysqli = new MySQLi('localhost', 'root', 'root', 'test2');

echo microtime(true);

//$mysqli = new mysqli();

//$stmt = $mysqli->prepare('SELECT * FROM table');

if (!@$mysqli->server_version)
{
    echo 'Not Connected';
}
else
{
    echo 'Connected';
}

$stmt = $mysqli->prepare('SELECT id, name, balance FROM account WHERE id = ?');
$id = 5;
$stmt->bind_param('i', $id);

echo '<pre>', var_dump($stmt->attr_get(MYSQLI_STMT_ATTR_PREFETCH_ROWS)), '</pre>';



exit;

$stmt1 = $mysqli->prepare('SELECT id, name, balance FROM account WHERE id = ?');
$stmt1->bind_param('i', $id_param);
$array = array();
$id = '';
$name = '';
$balance = '';
$array[] = &$id;
$array[] = &$name;
$array[] = &$balance;
call_user_func_array(array($stmt1, 'bind_result'), $array);
//$stmt1->bind_result($array);
$id_param = 1;
//$stmt->execute();

//$stmt->free_result();
$stmt1->execute();
echo '<pre>', var_dump($stmt1->error), '</pre>';

//exit;
unset($id, $name, $balance);

$stmt1->fetch();
echo '<pre>', var_dump($array), '</pre>';

exit;

/**
 * Testing transactions in MySQLi with prepared statements.
 *
 */
 
echo '<pre>', var_dump($mysqli->client_info), '</pre>';

$mysqli->autocommit(false);
$stmt1 = $mysqli->prepare('UPDATE account SET balance = balance + 100 WHERE id = 1');
$stmt2 = $mysqli->prepare('UPDATE account SET balance = balance - 100 WHERE id = 2');

$stmt1->execute();
$stmt2->execute();

exit;

/**
 * Testing transactions in MySQLi.
 *
 * When using START TRANSACTION.
 *
 * When there is an error in query, MySQLi does not throw any exception
 * or error that halts the current process.
 * 
 * If exception is thrown and script is terminated BEFORE 'COMMIT' command
 * is sent, it seems that MySQLi will do an implicit ROLLBACK. But we can
 * add $mysqli->query('ROLLBACK') in __destruct() in order to be safe.
 *
 */

$mysqli->autocommit(true);
$mysqli->query('START TRANSACTION');
if (!$mysqli->query('UPDATE account SET balance = balance + 100 WHERE id = 1'))
{
    echo '<pre>', var_dump($mysqli->error), '</pre>';
}
if (!$mysqli->query('UPDATE accounts SET balance = balance - 100 WHERE id = 2'))
{
    throw new Exception('Blah!');
    echo '<pre>', var_dump($mysqli->error), '</pre>';
}
$mysqli->query('COMMIT');
//$mysqli->query('COMMIT');
//$mysqli->query('ROLLBACK');

exit;

/**
 * This test proves that when a statement is 'executed', no other commands
 * can be issued until we call MySQLi_STMT::free_result() or MySQLi_STMT::reset()
 * I think it's safer to call free_result, then reset.
 *
 * MySQLi_STMT::reset does not unbind parameter. After you reset, you can safely
 * execute it again even if the query has parameters.
 * 
 * One way to learn that MySQLi_STMT has a result set or not is by using
 * result_metadata() function.
 * 
 * When you run a prepared statement that returns a result set, it locks the
 * connection unless you free_result() or store_result().
 *
 */

if ($mysqli->connect_error)
{
    echo '<pre>', var_dump($mysqli->connect_errno, $mysqli->connect_error), '</pre>';
}

$stmt = $mysqli->prepare('SELECT id, title, body FROM recipes');
$stmt->execute();

if (!$mysqli->query("INSERT INTO recipes (title, body) VALUES ('blah', 'bleh')"))
{
    echo '<pre>', var_dump($mysqli->error), '</pre>';
}

$stmt->bind_result($id, $title, $body);

while ($stmt->fetch())
{
    echo '<pre>', var_dump($id, $title, $body), '</pre>';
}

$stmt->execute();
//$stmt->reset();
//$stmt->free_result();

if (!$mysqli->query("INSERT INTO recipes (title, body) VALUES ('blah', 'bleh')"))
{
    echo '<pre>', var_dump($mysqli->error), '</pre>';
}