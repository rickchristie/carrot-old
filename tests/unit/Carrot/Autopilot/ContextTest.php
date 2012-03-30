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
     * Test the levels mechanism of the context.
     *
     */
    public function testLevels()
    {
        $context = new Context('*');
        $this->assertEquals(0, $context->getLevel());
        
        $context = new Context('Class:MySQLi');
        $this->assertEquals(0, $context->getLevel());
        
        $context = new Context('Namespace:Carrot\MySQLi\\');
        $this->assertEquals(1, $context->getLevel());
        
        $context = new Context('Class:Carrot\MySQLi\MySQLi*');
        $this->assertEquals(2, $context->getLevel());
    }
    
    /**
     * Test the specificity comparison mechanism.
     *
     */
    public function testSpecificity()
    {
        $wildcard = new Context('*');
        $greedyNamespace = new Context('Namespace:Carrot*');
        $deepGreedyNamespace = new Context('Namespace:Carrot\MySQLi*');
        $namespace = new Context('Namespace:Carrot\MySQLi');
        $greedyClass = new Context('Class:Carrot\MySQLi\MySQLi*');
        $class = new Context('Class:Carrot\MySQLi\MySQLi');
        
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($wildcard));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($namespace));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($class));
        
        $this->assertEquals(TRUE, $greedyNamespace->isMoreSpecificThan($wildcard));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($namespace));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($class));
        
        $this->assertEquals(TRUE, $deepGreedyNamespace->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $deepGreedyNamespace->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($namespace));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($class));
        
        $this->assertEquals(TRUE, $namespace->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $namespace->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $namespace->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($namespace));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($class));
        
        $this->assertEquals(TRUE, $greedyClass->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $greedyClass->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $greedyClass->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(TRUE, $greedyClass->isMoreSpecificThan($namespace));
        $this->assertEquals(FALSE, $greedyClass->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $greedyClass->isMoreSpecificThan($class));
        
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($namespace));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $class->isMoreSpecificThan($class));
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