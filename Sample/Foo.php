<?php

namespace Sample;

use Sample\Sec\Baz,
    Sample\Sec\Bar;

class Foo
{
    public function __construct(Baz $baz, Bar $bar)
    {
        $this->baz = $baz;
        $this->bar = $bar;
    }
}