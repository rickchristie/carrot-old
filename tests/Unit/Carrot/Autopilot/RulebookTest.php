<?php

namespace Carrot\Autopilot;

use PHPUnit_Framework_TestCase,
    Carrot\Autopilot\Instantiator\InstantiatorInterface,
    Carrot\Autopilot\Foo\Bar,
    Carrot\Autopilot\Foo\Ham,
    Carrot\Autopilot\Foo\Egg\Baz;

/**
 * Unit test for the Rulebook class.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class RulebookTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the automatic arguments generator.
     * 
     * @use Carrot\Autopilot\Identifier
     * @use Carrot\Autopilot\Context
     *
     */
    public function testAutoArgs()
    {
        $stringOne = 'One';
        $baz = new Baz;
        $bar = new Bar;
        $barIdentifier = new Identifier('Carrot\Autopilot\Foo\Bar@Default');
        $bazIdentifier = new Identifier('Carrot\Autopilot\Foo\Egg\Baz@Default');
        $hamIdentifier = new Identifier('Carrot\Autopilot\Foo\Ham@Default');
        $stringTwo = 'Two';
        
        $rulebook = new Rulebook;
        $context = new Context('Identifier:Carrot\Autopilot\Foo\Ham@Default');
        
        $rulebook->defineAutoVar(
            $context,
            'baz',
            $bazIdentifier
        );
        
        $rulebook->defineAutoVar(
            $context,
            'bar',
            $barIdentifier
        );
        
        $rulebook->defineAutoVar(
            $context,
            'stringTwo',
            $stringTwo
        );
        
        $rulebook->defineAutoVar(
            $context,
            'stringOne',
            $stringOne
        );
        
        $instantiator = $rulebook->generateInstantiator(
            $hamIdentifier
        );
        
        $this->assertEquals(TRUE, $instantiator instanceof InstantiatorInterface);
        $this->assertEquals(FALSE, $instantiator->isReadyForInjection());
        $list = $instantiator->getDependencyList();
        
        $list->setObject(
            $bazIdentifier->get(),
            $baz
        );
        
        $list->setObject(
            $barIdentifier->get(),
            $bar
        );
        
        $this->assertEquals(TRUE, $instantiator->isReadyForInjection());
        $ham = $instantiator->instantiate();
        $this->assertEquals(TRUE, $ham instanceof Ham);
        $this->assertEquals(TRUE, $bar === $ham->getBar());
        $this->assertEquals(TRUE, $baz === $ham->getBaz());
        $this->assertEquals($stringOne, $ham->getStringOne());
        $this->assertEquals($stringTwo, $ham->getStringTwo());
    }
    
    /**
     * Test the setter generator.
     * 
     * @use Carrot\Autopilot\Identifier
     * @use Carrot\Autopilot\Context
     *
     */
    public function testSetter()
    {
        
    }
    
    /**
     * Test the constructor manual override.
     * 
     * @use Carrot\Autopilot\Identifier
     * @use Carrot\Autopilot\Context
     *
     */
    public function testOverrideCtor()
    {
        
    }
    
    /**
     * Test the provider manual override.
     * 
     * @use Carrot\Autopilot\Identifier
     * @use Carrot\Autopilot\Context
     *
     */
    public function testOverrideProvider()
    {
        
    }
}