<?php

namespace Carrot\Message;

class Message implements MessageInterface
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
        return $this->replacePlaceholders($this->message);
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
}