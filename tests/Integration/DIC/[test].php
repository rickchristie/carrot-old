<?php

use Carrot\DependencyInjection\Reference,
    Carrot\DependencyInjection\Injector\ConstructorInjector,
    Carrot\DependencyInjection\Injector\ProviderInjector,
    Carrot\DependencyInjection\Injector\CallbackInjector;

use Carrot\Routing\HTTPURI;

# Set up autoloader

$autoloader = require 'autoloader.php';
$autoloader->register();

$uri = new HTTPURI(
    'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI'],
    $_GET
);

echo '<pre>$_GET = ', var_dump($_GET), '</pre>';
echo '<pre>', var_dump($uri->getScheme()), '</pre>';
echo '<pre>', var_dump($uri->getScheme()), '</pre>';

echo '<pre>', var_dump($uri->pathMatches('/^\\/bläh/u')), '</pre>';

?>
<a href="<?php echo $uri->get() ?>"><?php echo $uri->get() ?></a>
<?php
exit;

# Test URI

$string = 'http://www.ics.uci.edu/pub/ietf/uri/?br%C3%BCk=blük#Related';
//$string = 'xmpp:user@host/resource';
preg_match_all('/^(([^:\\/?#]+):)?(\\/\\/([^\\/?#]*))?([^?#]*)(\\\\?([^#]*))?(#(.*))?/', $string, $matches);
echo '<pre>', var_dump($matches), '</pre>';
echo '<pre>', var_dump(substr($matches[7][0], 1)), '</pre>';

parse_str(substr($matches[7][0], 1), $get);
echo '<pre>PARSE_STR = ', var_dump($get), '</pre>';

?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<a href="http://localhost/carrot-dev3/blah :bl@h/ bleh %2A[]{} blah/">Tryout</a><br>
<a href="http://www.realacademiaespañola.es/">Real academia española</a><br>
<a href="http://www.realacademiaespa%C3%B1ola.es/">Real academia española (urlencode)</a><br>
<a href="http://localhost/carrot-dev3/test.php?wort=br%C3%BCcke">This page</a>

<br><br>

<?php

echo '<pre>', var_dump($_GET), '</pre>';
echo '<pre>', var_dump($_SERVER), '</pre>';

/*
echo '<pre>', var_dump($_GET['wort'] == 'www.realacademiaespañola.es'), '</pre>';
$uri = new URI('http://rick:pwd@www.example.com::80/carrot/blah?asdf=s$dsdf@#adf');
$uri->appendPath('/dddd/sss');
echo $uri->getPath('carrot'), '<br>';
echo $uri->get();
*/

# Test dependency injection container

$config = require 'injection.php';
$container = new Carrot\DependencyInjection\Container($config);

$config->addInjector(
    new ProviderInjector(
        new Reference('Sample\Sec\Bar', 'Singleton', 'Main'),
        new Reference('Sample\BarProvider')
    )
);

$config->addInjector(
    new CallbackInjector(
        new Reference('Sample\Sec\Bar', 'Singleton', 'Main'),
        function($array, \Sample\Tri\Bacon $bacon)
        {
            $array[] = $bacon;
            return new \Sample\Sec\Bar($array);
        },
        array(
            array('blah'),
            new Reference('Sample\Tri\Bacon')
        )
    )
);

$config->addInjector(
    new ConstructorInjector(
        new Reference('Sample\Sec\Bar', 'Singleton', 'Tri'),
        array(
            array('geek', 'gook', 'gaak')
        )
    )
);

$config->bind(new Reference('Sample\Sec\Bar', 'Singleton', 'Main'), NULL, 'Sample');
$config->bind(new Reference('Sample\Sec\Bar', 'Singleton', 'Tri'), NULL, 'Sample\Tri');

$config->addInjector(
    new ConstructorInjector(
        new Reference('Sample\Tri\Ham'),
        array(
            'aaa',
            'eee',
            new Reference('Sample\Sec\Bar', 'Singleton', 'Main')
        )
    )
);

$foo = $container->get(new Reference('Sample\Foo'));

echo '<pre>FOO INSTANTIATED = ', var_dump($foo), '</pre>';

echo '<pre>', var_dump($_SERVER), '</pre>';
echo '<pre>', var_dump(getallheaders()), '</pre>';