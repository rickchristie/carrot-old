<?php

namespace Sample\Sec;

use Sample\Tri\Ham;

class Baz
{
    public function __construct(Spam $spam, Ham $ham)
    {
        $this->spam = $spam;
        $this->ham = $ham;
    }
}