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
use Carrot\Message\ErrorMessage;

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
    public function notEmpty($activeParamID, array $parameters, $args = NULL)
    {
        $value = $parameters[$activeParamID];
        
        if (is_integer($value) OR is_bool($value) OR is_float($value))
        {
            return $this->getValidResult();
        }
        
        if (is_string($value))
        {
            $value = trim($value);
            
            if ($value === '')
            {
                return $this->getInvalidResult($activeParamID);
            }
        }
        
        if (empty($value))
        {
            return $this->getInvalidResult($activeParamID);
        }
    }
    
    public function zendNotEmpty($activeParamID, array $parameters, array $args = array())
    {
        
    }
    
    protected function getValidResult()
    {
        $result = new ValidationResult;
        $result->setStatusToValid();
        return $result;
    }
    
    protected function getInvalidResult($activeParamID)
    {
        $message = new ErrorMessage(_("{@$activeParamID} must not be empty."));
        $message->setParameterID($activeParamID);
        $result = new ValidationResult;
        $result->setStatusToInvalid();
        $result->addMessage($message);
        return $result;
    }
}