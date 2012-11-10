<?php

namespace Carrot\Autopilot;

use PHPUnit_Framework_TestCase;

/**
 * Unit test for the Autopilot Identifier class.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class IdentifierTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests simple identifier object construction and the splitting
     * of values.
     *
     */
    public function testConstruction()
    {
        $ident = new Identifier('SampleClass@Default');
        $this->assertEquals('SampleClass@Default', $ident->get());
        $this->assertEquals('SampleClass', $ident->getClass());
        $this->assertEquals('SampleClass', $ident->getClassName());
        $this->assertEquals('Default', $ident->getName());
        $this->assertEquals(NULL, $ident->getNamespace());
        $this->assertEquals(TRUE, $ident->isNamespace(''));
        $this->assertEquals(TRUE, $ident->isNamespace('\\'));
        $this->assertEquals(FALSE, $ident->isNamespace('Foo'));
        $this->assertEquals(TRUE, $ident->isInNamespace(''));
        $this->assertEquals(TRUE, $ident->isInNamespace('\\'));
        $this->assertEquals(FALSE, $ident->isInNamespace('Foo'));
        
        $ident = new Identifier('Foo\Bar\SampleClass@Default');
        $this->assertEquals('Foo\Bar\SampleClass@Default', $ident->get());
        $this->assertEquals('Foo\Bar\SampleClass', $ident->getClass());
        $this->assertEquals('SampleClass', $ident->getClassName());
        $this->assertEquals('Default', $ident->getName());
        $this->assertEquals('Foo\Bar', $ident->getNamespace());
        $this->assertEquals(TRUE, $ident->isNamespace('Foo\Bar\\'));
        $this->assertEquals(TRUE, $ident->isNamespace('\Foo\Bar'));
        $this->assertEquals(TRUE, $ident->isNamespace('Foo\Bar'));
        $this->assertEquals(FALSE, $ident->isNamespace('Baz'));
        $this->assertEquals(TRUE, $ident->isInNamespace('Foo'));
        $this->assertEquals(TRUE, $ident->isInNamespace('Foo\Bar'));
        $this->assertEquals(TRUE, $ident->isInNamespace('Foo\Bar\\'));
        $this->assertEquals(FALSE, $ident->isInNamespace('Baz'));
    }
    
    /**
     * Tests the check class capabilities of Identifier objects.
     *
     */
    public function testCheckClass()
    {
        $ident = new Identifier('Carrot\Autopilot\Foo\Bar@Default');
        $bar = new Foo\Bar;
        $baz = new Foo\Egg\Baz;
        $mysqli = new \MySQLi;
        $this->assertEquals(TRUE, $ident->checkClass($bar));
        $this->assertEquals(TRUE, $ident->checkClass($baz));
        $this->assertEquals(FALSE, $ident->checkClass($mysqli));
    }
}