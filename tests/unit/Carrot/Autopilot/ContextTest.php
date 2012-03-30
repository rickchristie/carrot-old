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
        $this->assertEquals(TRUE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(FALSE, $context->hasConfigurationName());
        $this->assertEquals(FALSE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test class only context with configuration name.
     *
     */
    public function testClassWithConfigName()
    {
        $context = new Context('Class:Carrot\Autopilot\TestHelpers\Foo@Main');
        $this->assertEquals(TRUE, $context->isClass());
        $this->assertEquals(TRUE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(TRUE, $context->hasConfigurationName());
        $this->assertEquals(FALSE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo@Main');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo@Main:Transient');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test class only context with lifecycle setting.
     *
     */
    public function testClassWithLifecycle()
    {
        $context = new Context('Class:Carrot\Autopilot\TestHelpers\Foo:Singleton');
        $this->assertEquals(TRUE, $context->isClass());
        $this->assertEquals(TRUE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(FALSE, $context->hasConfigurationName());
        $this->assertEquals(TRUE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo@Main');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo@Main:Transient');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test atomic class context.
     *
     */
    public function testAtomicClass()
    {
        $context = new Context('Class:Carrot\Autopilot\TestHelpers\Foo@Main:Singleton');
        $this->assertEquals(TRUE, $context->isClass());
        $this->assertEquals(TRUE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(TRUE, $context->isAtomic());
        $this->assertEquals(TRUE, $context->hasConfigurationName());
        $this->assertEquals(TRUE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo@Main');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo@Main:Singleton');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo@Main:Transient');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(FALSE, $context->includes($reference));
        
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
        $this->assertEquals(TRUE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(TRUE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(FALSE, $context->hasConfigurationName());
        $this->assertEquals(FALSE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test greedy class context with configuration name.
     *
     */
    public function testGreedyClassWithConfigName()
    {
        $context = new Context('Class:Carrot\Autopilot\TestHelpers\Foo*@Main');
        $this->assertEquals(TRUE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(TRUE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(TRUE, $context->hasConfigurationName());
        $this->assertEquals(FALSE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo@Main');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar@Main');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test greedy class context with lifecycle setting.
     *
     */
    public function testGreedyClassWithLifecycle()
    {
        $context = new Context('Class:Carrot\Autopilot\TestHelpers\Foo*:Singleton');
        $this->assertEquals(TRUE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(TRUE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(FALSE, $context->hasConfigurationName());
        $this->assertEquals(TRUE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo@Main');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo:Transient');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar@Main');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test greedy class with both lifecycle setting and
     * configuration name defined.
     *
     */
    public function testGreedyClassWithFullSetting()
    {
        $context = new Context('Class:Carrot\Autopilot\TestHelpers\Foo*@Main:Singleton');
        $this->assertEquals(TRUE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(TRUE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(TRUE, $context->hasConfigurationName());
        $this->assertEquals(TRUE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo@Main');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Foo:Transient');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\Autopilot\TestHelpers\Bar@Main');
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
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(TRUE, $context->isNamespace());
        $this->assertEquals(TRUE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(FALSE, $context->hasConfigurationName());
        $this->assertEquals(FALSE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\MySQLi\MySQLi');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi\Child');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test that namespace typed context ignore configuration name.
     *
     */
    public function testNamespaceIgnoreConfigurationName()
    {
        $context = new Context('Namespace:Carrot\MySQLi@Main');
        $this->assertEquals(FALSE, $context->hasConfigurationName());
    }
    
    /**
     * Test that namespace typed context ignore lifecycle setting.
     *
     */
    public function testNamespaceIgnoreLifecycleSetting()
    {
        $context = new Context('Namespace:Carrot\MySQLi:Singleton');
        $this->assertEquals(FALSE, $context->hasLifecycleSetting());
    }
    
    /**
     * Test greedy namespace context.
     *
     */
    public function testGreedyNamespace()
    {
        $context = new Context('Namespace:Carrot\MySQLi*');
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(TRUE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(TRUE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(FALSE, $context->hasConfigurationName());
        $this->assertEquals(FALSE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\MySQLi\MySQLi');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi\Child');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi');
        $this->assertEquals(FALSE, $context->includes($reference));
        
        $context = new Context('Namespace:Carrot*');
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(TRUE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(TRUE, $context->isGreedyNamespace());
        $this->assertEquals(FALSE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(FALSE, $context->hasConfigurationName());
        $this->assertEquals(FALSE, $context->hasLifecycleSetting());
        
        $reference = new Reference('Carrot\MySQLi\MySQLi');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi\Child');
        $this->assertEquals(TRUE, $context->includes($reference));
        
        $reference = new Reference('Acme\MySQLi');
        $this->assertEquals(FALSE, $context->includes($reference));
    }
    
    /**
     * Test that greedy namespace ignore configuration name.
     *
     */
    public function testGreedyNamespaceIgnoreConfigurationName()
    {
        $context = new Context('Namespace:Carrot\MySQLi*@Main');
        $this->assertEquals(FALSE, $context->hasConfigurationName());
    }
    
    /**
     * Test that greedy namespace ignore lifecycle setting.
     *
     */
    public function testGreedyNamespaceIgnoreLifecycleSetting()
    {
        $context = new Context('Namespace:Carrot\MySQLi*:Singleton');
        $this->assertEquals(FALSE, $context->hasLifecycleSetting());
    }
    
    /**
     * Test wildcard context.
     *
     */
    public function testWildcard()
    {
        $context = new Context('*');
        $this->assertEquals(FALSE, $context->isClass());
        $this->assertEquals(FALSE, $context->isNonGreedyClass());
        $this->assertEquals(FALSE, $context->isGreedyClass());
        $this->assertEquals(FALSE, $context->isNamespace());
        $this->assertEquals(FALSE, $context->isNonGreedyNamespace());
        $this->assertEquals(FALSE, $context->isGreedyNamespace());
        $this->assertEquals(TRUE, $context->isWildcard());
        $this->assertEquals(FALSE, $context->isAtomic());
        $this->assertEquals(FALSE, $context->hasConfigurationName());
        $this->assertEquals(FALSE, $context->hasLifecycleSetting());
        
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
    public function testLevel()
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
        $greedyClassWithLc = new Context('Class:Carrot\MySQLi\MySQLi*:Singleton');
        $greedyClassWithCfg = new Context('Class:Carrot\MySQLi\MySQLi*@Main');
        $greedyClassWithBoth = new Context('Class:Carrot\MySQLi\MySQLi*@Main:Singleton');
        $class = new Context('Class:Carrot\MySQLi\MySQLi');
        $classWithLc = new Context('Class:Carrot\MySQLi\MySQLi:Singleton');
        $classWithCfg = new Context('Class:Carrot\MySQLi\MySQLi@Main');
        $atomic = new Context('Class:Carrot\MySQLi\MySQLi@Main:Singleton');
        
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($wildcard));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($namespace));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($class));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $wildcard->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $greedyNamespace->isMoreSpecificThan($wildcard));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($namespace));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($class));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $greedyNamespace->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $deepGreedyNamespace->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $deepGreedyNamespace->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($namespace));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($class));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $deepGreedyNamespace->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $namespace->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $namespace->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $namespace->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($namespace));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($class));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $namespace->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $greedyClass->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $greedyClass->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $greedyClass->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(TRUE, $greedyClass->isMoreSpecificThan($namespace));
        $this->assertEquals(FALSE, $greedyClass->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $greedyClass->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(FALSE, $greedyClass->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(FALSE, $greedyClass->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(FALSE, $greedyClass->isMoreSpecificThan($class));
        $this->assertEquals(FALSE, $greedyClass->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $greedyClass->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $greedyClass->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $greedyClassWithLc->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $greedyClassWithLc->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $greedyClassWithLc->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(TRUE, $greedyClassWithLc->isMoreSpecificThan($namespace));
        $this->assertEquals(TRUE, $greedyClassWithLc->isMoreSpecificThan($greedyClass));
        $this->assertEquals(FALSE, $greedyClassWithLc->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(FALSE, $greedyClassWithLc->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(FALSE, $greedyClassWithLc->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(FALSE, $greedyClassWithLc->isMoreSpecificThan($class));
        $this->assertEquals(FALSE, $greedyClassWithLc->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $greedyClassWithLc->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $greedyClassWithLc->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $greedyClassWithCfg->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $greedyClassWithCfg->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $greedyClassWithCfg->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(TRUE, $greedyClassWithCfg->isMoreSpecificThan($namespace));
        $this->assertEquals(TRUE, $greedyClassWithCfg->isMoreSpecificThan($greedyClass));
        $this->assertEquals(TRUE, $greedyClassWithCfg->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(FALSE, $greedyClassWithCfg->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(FALSE, $greedyClassWithCfg->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(FALSE, $greedyClassWithCfg->isMoreSpecificThan($class));
        $this->assertEquals(FALSE, $greedyClassWithCfg->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $greedyClassWithCfg->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $greedyClassWithCfg->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $greedyClassWithBoth->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $greedyClassWithBoth->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $greedyClassWithBoth->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(TRUE, $greedyClassWithBoth->isMoreSpecificThan($namespace));
        $this->assertEquals(TRUE, $greedyClassWithBoth->isMoreSpecificThan($greedyClass));
        $this->assertEquals(TRUE, $greedyClassWithBoth->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(TRUE, $greedyClassWithBoth->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(FALSE, $greedyClassWithBoth->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(FALSE, $greedyClassWithBoth->isMoreSpecificThan($class));
        $this->assertEquals(FALSE, $greedyClassWithBoth->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $greedyClassWithBoth->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $greedyClassWithBoth->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($namespace));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($greedyClass));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(TRUE, $class->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(FALSE, $class->isMoreSpecificThan($class));
        $this->assertEquals(FALSE, $class->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $class->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $class->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $classWithLc->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $classWithLc->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $classWithLc->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(TRUE, $classWithLc->isMoreSpecificThan($namespace));
        $this->assertEquals(TRUE, $classWithLc->isMoreSpecificThan($greedyClass));
        $this->assertEquals(TRUE, $classWithLc->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(TRUE, $classWithLc->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(TRUE, $classWithLc->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(TRUE, $classWithLc->isMoreSpecificThan($class));
        $this->assertEquals(FALSE, $classWithLc->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $classWithLc->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $classWithLc->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $classWithCfg->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $classWithCfg->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $classWithCfg->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(TRUE, $classWithCfg->isMoreSpecificThan($namespace));
        $this->assertEquals(TRUE, $classWithCfg->isMoreSpecificThan($greedyClass));
        $this->assertEquals(TRUE, $classWithCfg->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(TRUE, $classWithCfg->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(TRUE, $classWithCfg->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(TRUE, $classWithCfg->isMoreSpecificThan($class));
        $this->assertEquals(TRUE, $classWithCfg->isMoreSpecificThan($classWithLc));
        $this->assertEquals(FALSE, $classWithCfg->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $classWithCfg->isMoreSpecificThan($atomic));
        
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($wildcard));
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($greedyNamespace));
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($deepGreedyNamespace));
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($namespace));
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($greedyClass));
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($greedyClassWithLc));
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($greedyClassWithCfg));
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($greedyClassWithBoth));
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($class));
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($classWithLc));
        $this->assertEquals(TRUE, $atomic->isMoreSpecificThan($classWithCfg));
        $this->assertEquals(FALSE, $atomic->isMoreSpecificThan($atomic));
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