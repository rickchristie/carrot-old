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
     * Test the includes method of the context.
     * 
     * @uses Identifier
     *
     */
    public function testIncludesMethod()
    {
        $identifier = new Identifier('Foo\Bar@Main');
    }
}