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
 * Existence Validator
 * 
// ---------------------------------------------------------------
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Validation\Validator;

use Carrot\Validation\ValidationResult;
use Carrot\Message\ValidatorMessage;

class ExistenceValidator implements ValidatorInterface
{   
    public function getCallbacks()
    {
        return array(
            'existence.notEmpty' => array($this, 'notEmpty'),
            'existence.zendNotEmpty' => array($this, 'zendNotEmpty')
        );
    }
    
    /**
     * 
     *
     */
    public function notEmpty($activeValueID, array $parameters, $args = NULL)
    {
        $value = $parameters[$activeValueID];
        
        if (is_integer($value) OR is_bool($value) OR is_float($value))
        {
            return $this->getValidResult();
        }
        
        if (is_string($value))
        {
            $value = trim($value);
            
            if ($value === '')
            {
                return $this->getInvalidResult($activeValueID);
            }
        }
        
        if (empty($value))
        {
            return $this->getInvalidResult($activeValueID);
        }
        
        return $this->getValidResult();
    }
    
    public function zendNotEmpty($activeValueID, array $parameters, array $args = array())
    {
        
    }
    
    protected function getValidResult()
    {
        $result = new ValidationResult;
        $result->setStatusToValid();
        return $result;
    }
    
    protected function getInvalidResult($activeValueID)
    {
        $message = new ValidatorMessage(
            get_class($this),
            _("{@$activeValueID} must not be empty."),
            ValidatorMessage::ERROR
        );
        
        $message->setValueID($activeValueID);
        $result = new ValidationResult;
        $result->setStatusToInvalid();
        $result->addMessage($message);
        return $result;
    }
}