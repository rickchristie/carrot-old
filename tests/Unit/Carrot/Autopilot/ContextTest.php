<?php

namespace Carrot\Autopilot;

use PHPUnit_Framework_TestCase;

/**
 * Unit test for the Autopilot Context class.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class ContextTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Context construction and the values that it manages to
     * generate.
     *
     */
    public function testConstruction()
    {
        $context = new Context('*');
        $this->assertEquals(TRUE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isIdentifier());
        $this->assertEquals(FALSE, $context->isGreedy());
        $this->assertEquals('*', $context->get());
        $this->assertEquals(NULL, $context->getContent());
        
        $context = new Context('Class:Bar\Foo');
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(TRUE, $context->isClass());
        $this->assertEquals(TRUE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isIdentifier());
        $this->assertEquals(FALSE, $context->isGreedy());
        $this->assertEquals('Class:Bar\Foo', $context->get());
        $this->assertEquals('Bar\Foo', $context->getContent());
        
        $context = new Context('Class:Bar\Foo*');
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(TRUE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(TRUE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isIdentifier());
        $this->assertEquals(TRUE, $context->isGreedy());
        $this->assertEquals('Class:Bar\Foo*', $context->get());
        $this->assertEquals('Bar\Foo', $context->getContent());
        
        $context = new Context('Namespace:\\');
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(TRUE, $context->isNamespace());
        $this->assertEquals(TRUE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isIdentifier());
        $this->assertEquals(FALSE, $context->isGreedy());
        $this->assertEquals('Namespace:\\', $context->get());
        $this->assertEquals('', $context->getContent());
        
        $context = new Context('Namespace:Bar\Foo');
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(TRUE, $context->isNamespace());
        $this->assertEquals(TRUE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isIdentifier());
        $this->assertEquals(FALSE, $context->isGreedy());
        $this->assertEquals('Namespace:Bar\Foo', $context->get());
        $this->assertEquals('Bar\Foo', $context->getContent());
        
        $context = new Context('Namespace:Bar\Foo*');
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(TRUE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(TRUE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isIdentifier());
        $this->assertEquals(TRUE, $context->isGreedy());
        $this->assertEquals('Namespace:Bar\Foo*', $context->get());
        $this->assertEquals('Bar\Foo', $context->getContent());
        
        $context = new Context('Identifier:Foo@Main');
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(TRUE, $context->isIdentifier());
        $this->assertEquals(FALSE, $context->isGreedy());
        $this->assertEquals('Identifier:Foo@Main', $context->get());
        $this->assertEquals('Foo@Main', $context->getContent());
    }
    
    /**
     * Test the includes() method for class type contexts.
     * 
     * @use Carrot\Autopilot\Identifier
     *
     */
    public function testIncludesClass()
    {
        $identifier = new Identifier('Carrot\Autopilot\Foo\Bar@Main');
        $context = new Context('Class:Carrot\Autopilot\Foo\Bar');
        $this->assertEquals(TRUE, $context->includes($identifier));
        
        $context = new Context('Class:Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($identifier));
    }
    
    /**
     * Test the includes() method for greedy class type contexts.
     * 
     * @use Carrot\Autopilot\Identifier
     *
     */
    public function testIncludesClassGreedy()
    {
        $identifier = new Identifier('Carrot\Autopilot\Foo\Egg\Baz@Main');
        $context = new Context('Class:Carrot\Autopilot\Foo\Bar*');
        $this->assertEquals(TRUE, $context->includes($identifier));
        
        $context = new Context('Class:Carrot\Autopilot\Foo\Egg\Baz');
        $this->assertEquals(TRUE, $context->includes($identifier));
        
        $context = new Context('Class:Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($identifier));
    }
    
    /**
     * Test the includes() method for namespace type contexts.
     * 
     * @use Carrot\Autopilot\Identifier
     *
     */
    public function testIncludesNamespace()
    {
        $identifier = new Identifier('Carrot\Autopilot\Foo\Bar@Main');
        $context = new Context('Namespace:Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($identifier));
        
        $context = new Context('Namespace:Carrot\Autopilot\Foo');
        $this->assertEquals(TRUE, $context->includes($identifier));
        
        $context = new Context('Namespace:\\');
        $identifier = new Identifier('MySQLi@Main');
        $this->assertEquals(TRUE, $context->includes($identifier));
        
        $identifier = new Identifier('Carrot\Autopilot\Autopilot@Main');
        $this->assertEquals(FALSE, $context->includes($identifier));
    }
    
    /**
     * Test the includes() method for greedy namespace type contexts.
     * 
     * @use Carrot\Autopilot\Identifier
     *
     */
    public function testIncludesNamespaceGreedy()
    {
        $context = new Context('Namespace:Carrot\Autopilot\Foo*');
        $identifier = new Identifier('Carrot\Autopilot\Autopilot@Main');
        $this->assertEquals(FALSE, $context->includes($identifier));
        
        $identifier = new Identifier('Carrot\Autopilot\Foo\Bar@Main');
        $this->assertEquals(TRUE, $context->includes($identifier));
        
        $identifier = new Identifier('Carrot\Autopilot\Foo\Egg\Baz@Main');
        $this->assertEquals(TRUE, $context->includes($identifier));
    }
    
    /**
     * Test the includes() method of the identifier type contexts.
     * 
     * @use Carrot\Autopilot\Identifier
     *
     */
    public function testIncludesIdentifier()
    {
        $context = new Context('Identifier:Carrot\Autopilot\Foo\Bar@Default');
        $identifier = new Identifier('Carrot\Autopilot\Foo\Bar@Default');
        $this->assertEquals(TRUE, $context->includes($identifier));
        
        $identifier = new Identifier('Carrot\Autopilot\Foo\Bar@Main');
        $this->assertEquals(FALSE, $context->includes($identifier));
    }
}