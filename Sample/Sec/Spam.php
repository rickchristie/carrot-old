<?php

namespace Sample\Sec;

use Sample\Tri\Beef,
    Sample\Tri\Pork,
    Sample\Tri\Bacon;

class Spam
{
    public function __construct(Beef $beef, Pork $pork, Bacon $bacon)
    {
        $this->beef = $beef;
        $this->pork = $pork;
        $this->bacon = $bacon;
    }
}