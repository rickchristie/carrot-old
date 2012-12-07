<?php

namespace Carrot\Autopilot;

use PHPUnit_Framework_TestCase,
    Carrot\Autopilot\Foo\Bar;

/**
 * Unit test for the Collection class.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test everything in the collection class (it's simple). The
     * get(), set(), and has() method.
     *
     */
    public function testGetSetHas()
    {
        $collection = new Collection;
        $bar = new Bar;
        $egg = 'egg';
        
        $this->assertEquals(FALSE, $collection->has('bar'));
        $this->assertEquals(FALSE, $collection->has('egg'));
        
        $collection->set('bar', $bar);
        $collection->set('egg', $egg);
        
        $this->assertEquals(TRUE, $collection->has('bar'));
        $this->assertEquals(TRUE, $collection->has('egg'));
        $this->assertEquals(TRUE, $bar === $collection->get('bar'));
        $this->assertEquals($egg, $collection->get('egg'));
    }
}