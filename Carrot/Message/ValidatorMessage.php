<?php

namespace Carrot\Message;

class ValidatorMessage implements ValidatorMessageInterface
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
    
    public function getValueID()
    {
        return $this->valueID;
    }
    
    public function setValueID($valueID)
    {
        $this->valueID = $valueID;
    }
    
    public function setValueLabels(array $valueLabels)
    {
        $this->valueLabels = $valueLabels;
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
        return $this->replaceValueLabels($message);
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
    
    protected function replaceValueLabels($message)
    {   
        foreach ($this->valueLabels as $id => $label)
        {
            $pattern = "/{@$id}/";
            $message = preg_replace($pattern, $label, $message);
        }
        
        return $message;
    }
}