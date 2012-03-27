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