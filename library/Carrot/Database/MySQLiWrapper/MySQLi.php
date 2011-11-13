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
 * MySQLi Wrapper
 * 
 * This class extends the original MySQLi class and adds the
 * {@see buildStatement()} method, which acts as the factory
 * method to create {@see Statement} objects. Other than that,
 * this class doesn't alter the original MySQLi's behavior at
 * all, allowing you to use it also as a regular MySQLi class.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Database\MySQLiWrapper;

use RuntimeException,
    MySQLi as MySQLi_Parent;

class MySQLi extends MySQLi_Parent
{   
    /**
     * Creates an instance of Statement.
     *
     * Statement string sent must use placeholders. Placeholders are
     * marked by the colon character (:).
     * 
     * <code>
     * $statement = $mysqli->buildStatement(
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
     * So you can execute the statement like this:
     * 
     * <code>
     * $statement->execute(array(
     *     ':id' => $id,
     *     ':name' => $name
     *     ':balance' => $balance
     * ));
     * </code>
     * 
     * @see Statement
     * @param string $queryWithPlaceholders Statement string with
     *        placeholders.
     * @return Statement
     * 
     */
    public function buildStatement($queryWithPlaceholders)
    {
        $placeholders = $this->extractPlaceholders($queryWithPlaceholders);
        $query = $this->replacePlaceholdersWithQuestionMarks($queryWithPlaceholders);
        return new Statement($this, $query, $placeholders);
    }
    
    /**
     * Extracts placeholder names from original query.
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
     * @param string $queryWithPlaceholders The query string with placeholders.
     * @return array Array that contains placeholder names.
     *
     */
    protected function extractPlaceholders($queryWithPlaceholders)
    {
        preg_match_all('/:[a-zA-Z0-9_:]+/', $queryWithPlaceholders, $matches);
        
        if (isset($matches[0]) && is_array($matches[0]))
        {
            return $matches[0];
        }
        
        return array();
    }
    
    /**
     * Replaces placeholders (:string) with '?'.
     *
     * This in effect creates a query string that we can use to
     * instantiate a MySQLi statement object. It replaces this
     * pattern:
     *
     * <code>
     * :[a-zA-Z0-9_:]+
     * </code>
     *
     * with question mark ('?').
     *
     * @param string $queryWithPlaceholders
     * @return string Statement string safe to use as \MySQLi_STMT instantiation argument.
     *
     */
    protected function replacePlaceholdersWithQuestionMarks($queryWithPlaceholders)
    {
        return preg_replace('/:[a-zA-Z0-9_:]+/', '?', $queryWithPlaceholders);
    }
}