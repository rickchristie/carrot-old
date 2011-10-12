<?php

namespace Sample;

class BarProvider implements \Carrot\Core\DependencyInjection\Injector\ProviderInterface
{
    public function get()
    {
        return new \Sample\Sec\Bar(
            array(
                'hooohohoho'
            )
        );
    }
}