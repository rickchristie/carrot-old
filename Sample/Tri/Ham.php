<?php

namespace Sample\Tri;

class Ham
{
    public function __construct($arr, $err, \Sample\Sec\Bar $bar)
    {
        $this->arr = $arr;
        $this->err = $err;
        $this->bar = $bar;
    }
}