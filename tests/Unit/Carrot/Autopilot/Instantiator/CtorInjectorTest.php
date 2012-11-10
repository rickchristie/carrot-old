<?php

namespace Carrot\Autopilot\Instantiator;

use MySQLi,
    ReflectionMethod,
    ReflectionClass,
    PHPUnit_Framework_TestCase,
    Carrot\Autopilot\Identifier,
    Carrot\Autopilot\DependencyList;

/**
 * Unit test for the Autopilot CtorInjector class.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class CtorInjectorTest extends PHPUnit_Framework_TestCase
{
    public function testReflectionMethod()
    {
        $reflectionClass = new ReflectionClass('MySQLi');
        $constructor = $reflectionClass->getConstructor();
        $parameters = $constructor->getParameters();
    }
    
    public function testNormalInjection()
    {
        
    }
}