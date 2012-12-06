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
     * @see isGetBazCalled()
     * @var bool $isGetBazCalled
     */
    private $isGetBazCalled = FALSE;
    
    /**
     * @see isGetSpamCalled()
     * @var bool $isGetSpamCalled
     */
    private $isGetSpamCalled = FALSE;
    
    /**
     * Used to test provider injector.
     * 
     * @param string $stringOne
     * @param Baz $baz
     * @param stringTwo $stringTwo
     * 
     * @return Spam
     *
     */
    public function getSpam(
        Baz $baz
    )
    {
        $this->isGetSpamCalled = TRUE;
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
        $this->isGetBazCalled = TRUE;
        return new Baz;
    }
    
    /**
     * Returns TRUE if getBaz() method is called at least once.
     * 
     * @return bool
     *
     */
    public function isGetBazCalled()
    {
        return $this->isGetBazCalled;
    }
    
    /**
     * Returns TRUE if getSpam() method is called at least once.
     * 
     * @return bool
     *
     */
    public function isGetSpamCalled()
    {
        return $this->isGetSpamCalled;
    }
}