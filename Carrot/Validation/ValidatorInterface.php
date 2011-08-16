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
 * Validator Interface
 *
 * Implement this interface if you want to create your own set of
 * validators. Your validators must not contain any state and
 * should serve as collection of validating methods grouped
 * together in categories (e.g. string validators, etc).
 * 
// ---------------------------------------------------------------
 * For more information on how to implement this interface, you
 * can see some implementations on 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation;

interface ValidatorInterface
{
    /**
     * Get an array of validator callbacks, with their name.
     * 
    // ---------------------------------------------------------------
     * asdfasdf
     * 
     */
    public function getCallbacks();
    
    /**
     * Returns TRUE if the validator is a complex validator.
     * 
    // ---------------------------------------------------------------
     * This method is used by ValidatorChain to determine the type of
     * the validator.
     * 
     * @return bool TRUE if complex validator, FALSE if basic validator.
     *
     */
    public function isComplexValidator();
}