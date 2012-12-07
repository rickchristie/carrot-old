<?php

namespace Carrot\Autopilot;

use PHPUnit_Framework_TestCase,
    Carrot\Autopilot\Foo\Spam,
    Carrot\Autopilot\Foo\Egg\Scrambled,
    Carrot\Autopilot\Foo\Egg\Baz;

/**
 * Unit test for the Autopilot class.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class AutopilotTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test instantiation of object with no constructor arguments
     * an no rule defined.
     *
     */
    public function testCtorNoArgs()
    {
        /*
        $autopilot = new Autopilot;
        $scrambled = $autopilot->get('Carrot\Autopilot\Foo\Egg\Scrambled@Default');
        $this->assertEquals(TRUE, $scrambled instanceof Scrambled);
        */
    }
    
    /**
     * Test wildcard arguments.
     *
     */
    public function testAuto()
    {
        /*
        $autopilot = new Autopilot;
        $baz = new Baz;
        $autopilot->def('faux', 'fauxvalue');
        $autopilot->def('baz', $baz);
        $spam = $autopilot->get('Carrot\Autopilot\Foo\Spam@Default');
        $this->assertEquals(TRUE, $spam instanceof Spam);
        $this->assertEquals(TRUE, $baz === $spam->getBar());
        */
    }
    
    public function testAutoWithContext()
    {
        
    }
    
    public function testAutoWithSetter()
    {
        
    }
    
    public function testManualCtor()
    {
        
    }
    
    public function testManualCtorWithSetter()
    {
        
    }
    
    public function testManualProvider()
    {
        
    }
    
    public function testManualWithSetter()
    {
        
    }
}