<?php

namespace Carrot\Autopilot;

use PHPUnit_Framework_TestCase;

/**
 * Unit test for Carrot\Autopilot\Reference.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class ReferenceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test instantiating the class using complete reference ID.
     *
     */
    public function testFullParsing()
    {
        $reference = new Reference('Carrot\MySQLi\MySQLi@Main:Singleton');
        $this->assertEquals('Carrot\MySQLi\MySQLi', $reference->getClassName());
        $this->assertEquals('Main', $reference->getConfigurationName());
        $this->assertEquals(TRUE, $reference->isSingleton());
        $this->assertEquals('Carrot\MySQLi\MySQLi@Main:Singleton', $reference->getId());
        $this->assertEquals(TRUE, $reference->isLifecycle('Singleton'));
        $this->assertEquals(FALSE, $reference->isLifecycle('Transient'));
        $this->assertEquals(TRUE, $reference->isConfigurationName('Main'));
        $this->assertEquals(FALSE, $reference->isConfigurationName('Backup'));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi@Backup:Transient');
        $this->assertEquals('Carrot\MySQLi\MySQLi', $reference->getClassName());
        $this->assertEquals('Backup', $reference->getConfigurationName());
        $this->assertEquals(FALSE, $reference->isSingleton());
        $this->assertEquals('Carrot\MySQLi\MySQLi@Backup:Transient', $reference->getId());
        $this->assertEquals(FALSE, $reference->isLifecycle('Singleton'));
        $this->assertEquals(TRUE, $reference->isLifecycle('Transient'));
        $this->assertEquals(FALSE, $reference->isConfigurationName('Main'));
        $this->assertEquals(TRUE, $reference->isConfigurationName('Backup'));
    }
    
    /**
     * Test instantiating the class while omitting lifecycle setting.
     *
     */
    public function testOmittedLifecycle()
    {
        $reference = new Reference('Carrot\MySQLi\MySQLi@Backup');
        $this->assertEquals('Carrot\MySQLi\MySQLi', $reference->getClassName());
        $this->assertEquals('Backup', $reference->getConfigurationName());
        $this->assertEquals(TRUE, $reference->isSingleton());
        $this->assertEquals('Carrot\MySQLi\MySQLi@Backup:Singleton', $reference->getId());
        $this->assertEquals(TRUE, $reference->isLifecycle('Singleton'));
        $this->assertEquals(FALSE, $reference->isLifecycle('Transient'));
        $this->assertEquals(FALSE, $reference->isConfigurationName('Main'));
        $this->assertEquals(TRUE, $reference->isConfigurationName('Backup'));
    }
    
    /**
     * Test instantiating the class while omitting configuration name.
     *
     */
    public function testOmittedConfigurationName()
    {
        $reference = new Reference('Carrot\MySQLi\MySQLi:Singleton');
        $this->assertEquals('Carrot\MySQLi\MySQLi', $reference->getClassName());
        $this->assertEquals('', $reference->getConfigurationName());
        $this->assertEquals(TRUE, $reference->isSingleton());
        $this->assertEquals('Carrot\MySQLi\MySQLi@:Singleton', $reference->getId());
        $this->assertEquals(TRUE, $reference->isLifecycle('Singleton'));
        $this->assertEquals(FALSE, $reference->isLifecycle('Transient'));
        $this->assertEquals(TRUE, $reference->isConfigurationName(''));
        $this->assertEquals(FALSE, $reference->isConfigurationName('Backup'));
        
        $reference = new Reference('Carrot\MySQLi\MySQLi@:Transient');
        $this->assertEquals('Carrot\MySQLi\MySQLi', $reference->getClassName());
        $this->assertEquals('', $reference->getConfigurationName());
        $this->assertEquals(FALSE, $reference->isSingleton());
        $this->assertEquals('Carrot\MySQLi\MySQLi@:Transient', $reference->getId());
        $this->assertEquals(FALSE, $reference->isLifecycle('Singleton'));
        $this->assertEquals(TRUE, $reference->isLifecycle('Transient'));
        $this->assertEquals(TRUE, $reference->isConfigurationName(''));
        $this->assertEquals(FALSE, $reference->isConfigurationName('Backup'));
    }
    
    /**
     * Test instantiating the class while omitting both lifecycle
     * setting and configuration name.
     *
     */
    public function testOmittedBoth()
    {
        $reference = new Reference('Carrot\MySQLi\MySQLi');
        $this->assertEquals('Carrot\MySQLi\MySQLi', $reference->getClassName());
        $this->assertEquals('', $reference->getConfigurationName());
        $this->assertEquals(TRUE, $reference->isSingleton());
        $this->assertEquals('Carrot\MySQLi\MySQLi@:Singleton', $reference->getId());
        $this->assertEquals(TRUE, $reference->isLifecycle('Singleton'));
        $this->assertEquals(FALSE, $reference->isLifecycle('Transient'));
        $this->assertEquals(TRUE, $reference->isConfigurationName(''));
        $this->assertEquals(FALSE, $reference->isConfigurationName('Backup'));
    }
    
    /**
     * Makes sure that the class name returned is always consistent
     * (without backslash prefix and suffix).
     *
     */
    public function testClassNameCleaning()
    {
        $reference = new Reference('\Carrot\MySQLi\MySQLi\@Main:Singleton');
        $this->assertEquals('Carrot\MySQLi\MySQLi', $reference->getClassName());
    }
    
    /**
     * Tests invalid input.
     * 
     * @expectedException InvalidArgumentException
     *
     */
    public function testInvalidInput()
    {
        $reference = new Reference('Carrot\MySQLi.');
    }
}