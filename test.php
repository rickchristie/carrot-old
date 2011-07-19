<?php


class ControllerProvider
{
    /**
     * Constructs the controller provider.
     *
     * @param Config $config Configuration object
     * @param Blah $blah Blah instance
     * @inject Carrot\Core\Config@Main:Singleton
     * @inject Carrot\Core\Blah@Custom:Transient
     *
     */
    public function __construct(Config $config, Blah $blah)
    {
        
    }
}

$reflection = new ReflectionMethod('ControllerProvider', '__construct2');
$doc = $reflection->getDocComment();

exit;

$result = preg_match_all('/(@inject)[ \t]+([A-Za-z_\\\\].+)@([A-Za-z_].+):(Singleton|Transient)/', $doc, $matches);

echo '<pre>', var_dump($matches), '</pre>';
echo '<pre>', var_dump($reflection->getDocComment()), '</pre>';