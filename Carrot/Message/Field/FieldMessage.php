<?php

namespace Carrot\Message\Field;

class FieldMessage implements FieldMessageInterface
{
    protected $issuer;
    
    protected $message;
    
    protected $type;
    
    protected $code;
    
    protected $placeholders = array();
    
    public function __construct($issuer, $message, $type = self::INFORMATIONAL, $code = NULL)
    {
        $this->issuer = $issuer;
        $this->message = $message;
        $this->type = $type;
        $this->code = $code;
    }
    
    public function getIssuer()
    {
        return $this->issuer;
    }
    
    public function getMessage()
    {
        return $this->message;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function getFieldID()
    {
        return $this->fieldID;
    }
    
    public function setFieldID($fieldID)
    {
        $this->fieldID = $fieldID;
    }
    
    public function setFieldLabels(array $fieldLabels)
    {
        $this->fieldLabels = $fieldLabels;
    }
    
    public function setMessage($message)
    {
        $this->message = $message;
    }
    
    public function setPlaceholders(array $placeholders)
    {
        $this->placeholders = $placeholders;
    }
    
    public function get()
    {
        $message = $this->replacePlaceholders($this->message);
        return $this->replaceFieldLabels($message);
    }
    
    /**
     * Replace placeholder names with their respective replacement strings.
     * 
     * If the placeholder replacement is not set, the placeholder
     * syntax will not be replaced.
     * 
     * @param string $message The string that contains the placeholders syntax.
     * @return string The formatted message.
     *
     */
    protected function replacePlaceholders($message)
    {
        foreach ($this->placeholders as $name => $replacement)
        {
            $pattern = "/{:$name}/";
            $message = preg_replace($pattern, $replacement, $message);
        }
        
        return $message;
    }
    
    protected function replaceFieldLabels($message)
    {   
        foreach ($this->fieldLabels as $id => $label)
        {
            $pattern = "/{@$id}/";
            $message = preg_replace($pattern, $label, $message);
        }
        
        return $message;
    }
}