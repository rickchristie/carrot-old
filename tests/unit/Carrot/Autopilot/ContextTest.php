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
        $this->assertEquals(TRUE, $context->isClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test greedy class context.
     *
     */
    public function testGreedyClass()
    {
        $context = new Context('Class:Carrot\Autopilot\TestHelpers\Foo*');
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(TRUE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test namespace context.
     *
     */
    public function testNamespace()
    {
        $context = new Context('Namespace:Carrot\MySQLi');
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(TRUE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        
        $reference = new Reference('Carrot\MySQLi\MySQLi');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi\Child');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test greedy namespace context.
     *
     */
    public function testGreedyNamespace()
    {
        $context = new Context('Namespace:Carrot\MySQLi*');
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(TRUE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        
        $reference = new Reference('Carrot\MySQLi\MySQLi');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi\Child');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $context = new Context('Namespace:Carrot*');
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(TRUE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        
        $reference = new Reference('Carrot\MySQLi\MySQLi');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi\Child');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Acme\MySQLi');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test wildcard context.
     *
     */
    public function testWildcard()
    {
        $context = new Context('*');
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(TRUE, $context->isWildcard());
        
        $reference = new Reference('Carrot\MySQLi\MySQLi');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi\Child');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi');
        $this->assertEquals(TRUE, $context->includes($reference));
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