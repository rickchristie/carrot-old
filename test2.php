<?php

interface MessageInterface
{
    const INFORMATIONAL = '1';
    
    public function __construct($message, $type = self::INFORMATIONAL);
    
    public function get();
    
    public function setPlaceholders();
}

interface FieldMessageInterface extends MessageInterface
{
    public function setFieldID($fieldID);
    
    public function setParameterLabels();
}

class blah implements MessageInterface
{
    public function __construct($message, $type = self::INFORMATIONAL)
    {
        echo '<pre>', var_dump($type), '</pre>';
    }
    
    public function get() {
        
    }
    
    public function setPlaceholders() {
        
    }
}

$blah = new Blah('aa');