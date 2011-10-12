<?php

namespace Sample\Tri;

class Beef
{
    public function __construct(\Sample\Sec\Bar $bar)
    {
        $this->bar = $bar;
    }
}