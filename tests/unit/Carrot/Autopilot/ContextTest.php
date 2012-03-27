<?php

namespace Carrot\Autopilot;

use PHPUnit_Framework_TestCase,
    InvalidArgumentException;

/**
 * Unit testing for Carrot\Autopilot\Context.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class ContextTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test class only context.
     *
     */
    public function testClass()
    {
        $context = new Context('Class:Carrot\Autopilot\TestHelpers\Foo');
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test class with children context.
     *
     */
    public function testClassWildcard()
    {
        $context = new Context('Class:Carrot\Autopilot\TestHelpers\Foo*');
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test direct member namespace context.
     *
     */
    public function testNamespace()
    {
        $context = new Context('Namespace:Carrot\MySQLi');
        
        $reference = new Reference('Carrot\MySQLi\MySQLi');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi\Child');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test all members of namespace context.
     *
     */
    public function testNamespaceWildcard()
    {
        $context = new Context('Namespace:Carrot\MySQLi*');
        
        $reference = new Reference('Carrot\MySQLi\MySQLi');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi\Child');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test invalid input in context creation.
     * 
     * @expectedException InvalidArgumentException
     *
     */
    public function testInvalidInput()
    {
        $context = new Context('Carrot\MySQLi\MySQLi');       
    }
}