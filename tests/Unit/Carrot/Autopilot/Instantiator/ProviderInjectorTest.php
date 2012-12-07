<?php

namespace Carrot\Autopilot\Instantiator;

use PHPUnit_Framework_TestCase,
    Carrot\Autopilot\Identifier,
    Carrot\Autopilot\DependencyList,
    Carrot\Autopilot\Foo\Provider,
    Carrot\Autopilot\Foo\Bar,
    Carrot\Autopilot\Foo\Ham,
    Carrot\Autopilot\Foo\Spam,
    Carrot\Autopilot\Foo\Egg\Baz;

/**
 * Unit test for the Autopilot ProviderInjector class.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class ProviderInjectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test provider injection without arguments.
     * 
     * @use Carrot\Autopilot\Identifier
     * @use Carrot\Autopilot\DependencyList
     *
     */
    public function testInjectionNoArgs()
    {
        $providerIdentifier = new Identifier('Carrot\Autopilot\Foo\Provider@Default');
        $bazIdentifier = new Identifier('Carrot\Autopilot\Foo\Egg\Baz@Default');
        $providerInjector = new ProviderInjector(
            $bazIdentifier,
            $providerIdentifier,
            'getBaz'
        );
        
        $provider = new Provider;
        $this->assertEquals(FALSE, $providerInjector->isReadyForInjection());
        $list = $providerInjector->getDependencyList();
        $this->assertEquals(TRUE, $list instanceof DependencyList);
        
        $list->setObject(
            'Carrot\Autopilot\Foo\Provider@Default',
            $provider
        );
        
        $this->assertEquals(TRUE, $providerInjector->isReadyForInjection());
        $baz = $providerInjector->instantiate();
        $this->assertEquals(TRUE, $provider->isGetBazCalled());
        $this->assertEquals(TRUE, $baz instanceof Baz);
    }
    
    /**
     * Test provider injection with arguments.
     * 
     * @use Carrot\Autopilot\Identifier
     * @use Carrot\Autopilot\DependencyList
     *
     */
    public function testInjectionArgs()
    {
        $providerIdentifier = new Identifier('Carrot\Autopilot\Foo\Provider@Default');
        $bazIdentifier = new Identifier('Carrot\Autopilot\Foo\Egg\Baz@Default');
        $spamIdentifier = new Identifier('Carrot\Autopilot\Foo\Spam@Default');
        $stringOne = 'ONE';
        $stringTwo = 'TWO';
        $providerInjector = new ProviderInjector(
            $spamIdentifier,
            $providerIdentifier,
            'getSpam',
            array(
                'baz' => $bazIdentifier,
                'stringTwo' => $stringTwo,
                'stringOne' => $stringOne
            )
        );
        
        $provider = new Provider;
        $baz = new Baz;
        $list = $providerInjector->getDependencyList();
        $this->assertEquals(FALSE, $providerInjector->isReadyForInjection());
        $this->assertEquals(TRUE, $list instanceof DependencyList);
        
        $list->setObject(
            'Carrot\Autopilot\Foo\Provider@Default',
            $provider
        );
        
        $list->setObject(
            'Carrot\Autopilot\Foo\Egg\Baz@Default',
            $baz
        );
        
        $this->assertEquals(TRUE, $providerInjector->isReadyForInjection());
        $spam = $providerInjector->instantiate();
        $this->assertEquals(TRUE, $provider->isGetSpamCalled());
        $this->assertEquals(TRUE, $spam instanceof Spam);
    }
}