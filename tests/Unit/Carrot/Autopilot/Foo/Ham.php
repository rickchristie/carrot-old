<?php

namespace Carrot\Autopilot\Foo;

use Carrot\Autopilot\Foo\Bar,
    Carrot\Autopilot\Foo\Egg\Baz;

/**
 * Used for testing Autopilot library.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Ham
{
    /**
     * @see getBar()
     * @var Bar $bar
     */
    private $bar;
    
    /**
     * @see getStringOne()
     * @var string $stringOne
     */
    private $stringOne;
    
    /**
     * @see getBaz()
     * @var Baz $baz
     */
    private $baz;
    
    /**
     * @see getStringTwo()
     * @var string $stringTwo
     */
    private $stringTwo;
    
    /**
     * Used to test constructor injection.
     * 
     * @param Bar $bar
     * @param string $stringOne
     * @param Baz $baz
     * @param string $stringTwo
     *
     */
    public function __construct(
        Bar $bar,
        $stringOne,
        Baz $baz,
        $stringTwo
    )
    {
        $this->bar = $bar;
        $this->stringOne = $stringOne;
        $this->baz = $baz;
        $this->stringTwo = $stringTwo;
    }
    
    /**
     * Used to test CtorInjector.
     * 
     * @return Bar
     *
     */
    public function getBar()
    {
        return $this->bar;
    }
    
    /**
     * Used to test CtorInjector.
     * 
     * @return Baz
     *
     */
    public function getBaz()
    {
        return $this->baz;
    }
    
    /**
     * Used to test CtorInjector.
     * 
     * @return string
     *
     */
    public function getStringOne()
    {
        return $this->stringOne;
    }
    
    /**
     * Used to test CtorInjector.
     * 
     * @return string
     *
     */
    public function getStringTwo()
    {
        return $this->stringTwo;
    }
}