<?php

namespace Carrot\Autopilot\Foo;

use Carrot\Autopilot\Foo\Egg\Baz;

/**
 * Used to test ProviderInjector.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Provider
{
    /**
     * Used to test provider injector.
     * 
     * @param Baz $baz
     * @return Spam
     *
     */
    public function getSpam(Baz $baz)
    {
        return new Spam($baz);
    }
    
    /**
     * Used to test provider injector.
     * 
     * @return Baz
     *
     */
    public function getBaz()
    {
        return new Baz;
    }
}