<?php

namespace Carrot\Message\Field;

use Carrot\Message\MessageInterface;

interface FieldMessageInterface extends MessageInterface
{
    public function setFieldID($fieldID);
    
    public function getFieldID();
    
    public function setFieldLabels(array $labels);
}