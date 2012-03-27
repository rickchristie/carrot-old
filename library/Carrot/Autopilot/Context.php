<?php

namespace Carrot\Autopilot;

/**
 * Represents that contexts used in rulebooks.
 * 
 * Several of Autopilot's rulebook requires the usage of dynamic
 * context strings, which tells the namespace or class in which
 * the rule takes place. This object represents them.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Context
{
    private $string;
    
    /**
     * Constructor.
     *
     */
    public function __construct($string)
    {
        
    }
    
    public function appliesTo(Reference $reference)
    {
        
    }
}