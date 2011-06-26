<?php

namespace Carrot\Core\Interfaces;

interface ProviderInterface
{
    public function get(\Carrot\Core\DependencyInjectionContainer $dic = null);
}