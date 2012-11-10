<?php

namespace Carrot\Autopilot;

use PHPUnit_Framework_TestCase;

/**
 * Unit test for the Autopilot DependencyList class.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class DependencyListTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test object construction, plus getter and setter.
     * 
     * @use Carrot\Autopilot\Identifier
     *
     */
    public function testConstruction()
    {
        $barIdent = new Identifier('Carrot\Autopilot\Foo\Bar@Default');
        $bazIdent = new Identifier('Carrot\Autopilot\Foo\Egg\Baz@Default');
        $listArray = array($barIdent, $bazIdent);
        $resultArray = array(
            'Carrot\Autopilot\Foo\Bar@Default' => $barIdent,
            'Carrot\Autopilot\Foo\Egg\Baz@Default' => $bazIdent
        );
        
        $list = new DependencyList($listArray);
        $this->assertEquals($resultArray, $list->getList());
        $this->assertEquals(FALSE, $list->isFulfilled());
        
        $bar = new Foo\Bar;
        $baz = new Foo\Egg\Baz;
        $spam = new Foo\Spam($bar);
        
        $list->setObject('Carrot\Autopilot\Foo\Bar@Default', $bar);
        $this->assertEquals(FALSE, $list->isFulfilled());
        $this->assertEquals($bar, $list->getObject('Carrot\Autopilot\Foo\Bar@Default'));
        
        $list->setObject('Carrot\Autopilot\Foo\Egg\Baz@Default', $baz);
        $this->assertEquals($baz, $list->getObject('Carrot\Autopilot\Foo\Egg\Baz@Default'));
        $this->assertEquals(TRUE, $list->isFulfilled());
        
        $spamIdent = new Identifier('Carrot\Autopilot\Foo\Spam@Default');
        $list->add($spamIdent);
        $this->assertEquals(FALSE, $list->isFulfilled());
        $list->setObject('Carrot\Autopilot\Foo\Spam@Default', $spam);
        $this->assertEquals(TRUE, $list->isFulfilled());
    }
}