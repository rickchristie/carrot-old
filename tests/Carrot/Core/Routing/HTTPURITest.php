<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Tests for HTTPURI.
 * 
//---------------------------------------------------------------
 * 
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Routing\Tests;

use Carrot\Core\Routing\HTTPURI,
    PHPUnit_Framework_TestCase;

class HTTPURITest extends PHPUnit_Framework_TestCase
{   
    /**
     * Make sure the constructor receives normal input and parses
     * them accordingly.
     *
     */
    public function testConstructorNormalInput()
    {   
        $uri = new HTTPURI(
            'http',
            'example.com',
            '/path/subpath/',
        );
        
        
    }
    
    public function testNewArrayIsEmpty()
    {
        // Create the Array fixture.
        $fixture = array();
 
        // Assert that the size of the Array fixture is 0.
        $this->assertEquals(0, sizeof($fixture));
    }
 
    public function testArrayContainsAnElement()
    {
        // Create the Array fixture.
        $fixture = array();
 
        // Add an element to the Array fixture.
        $fixture[] = 'Element';
 
        // Assert that the size of the Array fixture is 1.
        $this->assertEquals(1, sizeof($fixture));
    }
}