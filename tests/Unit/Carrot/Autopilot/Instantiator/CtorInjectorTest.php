<?php

namespace Carrot\Autopilot\Instantiator;

use PHPUnit_Framework_TestCase,
    Carrot\Autopilot\Identifier,
    Carrot\Autopilot\DependencyList,
    Carrot\Autopilot\Foo\Bar,
    Carrot\Autopilot\Foo\Ham,
    Carrot\Autopilot\Foo\Provider,
    Carrot\Autopilot\Foo\Egg\Baz;

/**
 * Unit test for the Autopilot CtorInjector class.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class CtorInjectorTest extends PHPUnit_Framework_TestCase
{   
    /**
     * Test normal constructor injection on user class.
     * 
     * @use Carrot\Autopilot\Identifier
     * @use Carrot\Autopilot\DependencyList
     *
     */
    public function testNormalInjection()
    {
        $identifier = new Identifier('Carrot\Autopilot\Foo\Ham@Default');
        $barIdentifier = new Identifier('Carrot\Autopilot\Foo\Bar@Default');
        $bazIdentifier = new Identifier('Carrot\Autopilot\Foo\Egg\Baz@Default');
        $stringOne = 'One';
        $stringTwo = 'Twofer';
        $args = array(
            'stringTwo' => $stringTwo,
            'stringOne' => $stringOne,
            'baz' => $bazIdentifier,
            'bar' => $barIdentifier
        );
        
        $ctorInjector = new CtorInjector($identifier, $args);
        $baz = new Baz;
        $bar = new Bar;
        $list = $ctorInjector->getDependencyList();
        
        $this->assertEquals(TRUE, $list instanceof DependencyList);
        $this->assertEquals(TRUE, $identifier === $ctorInjector->getIdentifier());
        $this->assertEquals(FALSE, $ctorInjector->isReadyForInjection());
        
        $list->setObject(
            $bazIdentifier->get(),
            $baz
        );
        
        $list->setObject(
            $barIdentifier->get(),
            $bar
        );
        
        $this->assertEquals(TRUE, $ctorInjector->isReadyForInjection());
        $ham = $ctorInjector->instantiate();
        $this->assertEquals(TRUE, $ham instanceof Ham);
        $this->assertEquals($stringOne, $ham->getStringOne());
        $this->assertEquals($stringTwo, $ham->getStringTwo());
        $this->assertEquals(TRUE, $ham->getBaz() === $baz);
        $this->assertEquals(TRUE, $ham->getBar() === $bar);
    }
    
    /**
     * Test constructor injection on empty constructor.
     * 
     * @use Carrot\Autopilot\Identifier
     * @use Carrot\Autopilot\DependencyList
     *
     */
    public function testEmptyConstructor()
    {
        $identifier = new Identifier('Carrot\Autopilot\Foo\Egg\Baz@Default');
        $args = array(
            'random' => 466,
            'things' => 678
        );
        
        $ctorInjector = new CtorInjector($identifier, $args);
        $this->assertEquals(TRUE, $ctorInjector->isReadyForInjection());
        $baz = $ctorInjector->instantiate();
        $this->assertEquals(TRUE, $baz instanceof Baz);
    }
    
    /**
     * Test constructor injection if there is no constructor.
     * 
     * @use Carrot\Autopilot\Identifier
     * @use Carrot\Autopilot\DependencyList
     *
     */
    public function testNoConstructor()
    {
        $identifier = new Identifier('Carrot\Autopilot\Foo\Provider@Default');
        $ctorInjector = new CtorInjector($identifier);
        $list = $ctorInjector->getDependencyList();
        
        $this->assertEquals(TRUE, $ctorInjector->isReadyForInjection());
        $provider = $ctorInjector->instantiate();
        $this->assertEquals(TRUE, $provider instanceof Provider);
    }
}