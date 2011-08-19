<?php

namespace Carrot\Message;

interface ValidatorMessageInterface extends MessageInterface
{
    public function setValueID($fieldID);
    
    /**
     * Get value ID
     *
     * Should return FALSE if not set yet.
     *
     */
    public function getValueID();
    
    public function setValueLabels(array $labels);
}