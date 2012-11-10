<?php

namespace Carrot\Autopilot\Foo;

use StdClass,
    Carrot\Autopilot\Foo\Egg\Baz;

/**
 * Used to test Autopilot.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Bar
{
    /**
     * @see setBaz()
     * @var Baz $baz
     */
    private $baz;
    
    /**
     * @see setObject()
     * @var StdClas $object
     */
    private $object;
    
    /**
     * @see setString()
     * @var string $string
     */
    private $string;
    
    /**
     * @see getStringOne()
     * @var string $stringOne
     */
    private $stringOne;
    
    /**
     * @see getStringTwo()
     * @var string $stringTwo
     */
    private $stringTwo;
    
    /**
     * @see getStringThree()
     * @var string $stringThree
     */
    private $stringThree;
    
    /**
     * @see getStringDefault()
     * @var string $stringDefault
     */
    private $stringDefault;
    
    /**
     * Used to test setter injector.
     * 
     * @param Baz $baz
     *
     */
    public function setBaz(
        $stringOne,
        Baz $baz,
        $stringTwo,
        $stringThree,
        $stringDefault = 'default'
    )
    {
        $this->stringOne = $stringOne;
        $this->stringTwo = $stringTwo;
        $this->stringThree = $stringThree;
        $this->stringDefault = $stringDefault;
        $this->baz = $baz;
    }
    
    /**
     * Used to test setter injector.
     * 
     * @return Baz|NULL
     *
     */
    public function getBaz()
    {
        return $this->baz;
    }
    
    /**
     * Used to test setter injector.
     * 
     * @param StdClass $object
     *
     */
    public function setObject(StdClass $object)
    {
        $this->object = $object;
    }
    
    /**
     * Used to test setter injector.
     * 
     * @return StdClass|NULL
     *
     */
    public function getObject()
    {
        return $this->object;
    }
    
    /**
     * Used to test setter injector.
     * 
     * @param string $string
     *
     */
    public function setString($string)
    {
        $this->string = $string;
    }
    
    /**
     * Used to test setter injector.
     * 
     * @return string
     *
     */
    public function getString()
    {
        return $this->string;
    }
    
    /**
     * Used to test setter injector.
     *
     */
    public function emptySetter()
    {
        // San cai, san cai.
    }
    
    /**
     * Used to test setter injector.
     * 
     * @see setBaz()
     * @return string|NULL
     *
     */
    public function getStringOne()
    {
        return $this->stringOne;
    }
    
    /**
     * Used to test setter injector.
     * 
     * @see setBaz()
     * @return string|NULL
     *
     */
    public function getStringTwo()
    {
        return $this->stringTwo;
    }
    
    /**
     * Used to test setter injector.
     * 
     * @see setBaz()
     * @return string|NULL
     *
     */
    public function getStringThree()
    {
        return $this->stringThree;
    }
    
    /**
     * Used to test setter injector.
     * 
     * @see setBaz()
     * @return string|NULL
     *
     */
    public function getStringDefault()
    {
        return $this->stringDefault;
    }
}