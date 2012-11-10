<?php

namespace Carrot\Autopilot\Foo;

/**
 * Test class for Autopilot.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Spam
{
    /**
     * @see __construct()
     * @var Bar $bar
     */
    private $bar;
    
    /**
     * Constructor.
     * 
     * @param Bar $bar
     *
     */
    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}