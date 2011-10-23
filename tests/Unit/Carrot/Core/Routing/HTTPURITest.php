<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Tests for HTTPURI.
 * 
 * TODO: Several changes has been applied to HTTPURI class,
 *       including the addition of HTTPURI::getPathEncoded()
 *       and $removeQueryString boolean parameter in path
 *       operation methods. Update this class to reflect this
 *       change!
 *
 * TODO: Better organize the class according to 3A, arrange,
 *       action, assert. Create atomic test methods instead of
 *       big methods for better readability and maintainability.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Routing\Tests;

use Exception,
    InvalidArgumentException,
    PHPUnit_Framework_TestCase,
    Carrot\Core\Routing\HTTPURI;

class HTTPURITest extends PHPUnit_Framework_TestCase
{
    /**
     * According to HTTP specification (RFC 2616) and URI
     * specification (RFC 3986), the scheme and authority part must
     * not be empty, and the path must contain at least a 'slash'.
     *
     */
    public function testConstructorDoesNotAllowEmptyValues()
    {
        $exceptionsThrown = 0;
        
        try
        {
            $uri = new HTTPURI('', 'authority', 'path');
        }
        catch (InvalidArgumentException $exception)
        {
            $exceptionsThrown++;
        }
        
        try
        {
            $uri = new HTTPURI('scheme', '', 'path');
        }
        catch (InvalidArgumentException $exception)
        {
            $exceptionsThrown++;
        }
        
        try
        {
            $uri = new HTTPURI('scheme', 'authority', '');
        }
        catch (InvalidArgumentException $exception)
        {
            $exceptionsThrown++;
        }
        
        if ($exceptionsThrown != 3)
        {
            throw new Exception('HTTPURI must not allow empty values for scheme, authority, and path, as per specifications.');
        }
    }
    
    /**
     * According to HTTP specification (RFC 2616) and URI
     * specification (RFC 3986), the scheme and authority part must
     * not be empty, and the path must contain at least a 'slash'.
     *
     */
    public function testSetterDoesNotAllowEmptyInput()
    {
        $exceptionsThrown = 0;
        
        $uri = new HTTPURI(
            'http',
            'example.com',
            '/path/subpath/'
        );
        
        try
        {
            $uri->setScheme('');
        }
        catch (InvalidArgumentException $exception)
        {
            $exceptionsThrown++;
        }
        
        try
        {
            $uri->setAuthority('');
        }
        catch (InvalidArgumentException $exception)
        {
            $exceptionsThrown++;
        }
        
        try
        {
            $uri->setPath('');
        }
        catch (InvalidArgumentException $exception)
        {
            $exceptionsThrown++;
        }
        
        if ($exceptionsThrown != 3)
        {
            throw new Exception('HTTPURI must not allow empty values for scheme, authority, and path, as per specifications.');
        }
    }
    
    /**
     * Tests the getters and setters with general constructor
     * arguments.
     *
     */
    public function testGettersAndSetters()
    {   
        $uri = new HTTPURI(
            'http',
            'example.com',
            'path/subpath/'
        );
        
        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('example.com', $uri->getAuthority());
        $this->assertEquals('/path/subpath/', $uri->getPath());
        $this->assertEquals('', $uri->getQueryAsString());
        $this->assertEquals(array(), $uri->getQueryAsArray());
        $this->assertEquals('', $uri->getFragment());
        $this->assertEquals('http://example.com/path/subpath/', $uri->get());
        
        $uri->setScheme('https');
        $uri->setAuthority('example.org');
        $uri->setPath('another/subpath');
        $uri->setQuery(array('keyA' => 'valueA'));
        $uri->setFragment('heading-1');
        
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('example.org', $uri->getAuthority());
        $this->assertEquals('/another/subpath', $uri->getPath());
        $this->assertEquals('keyA=valueA', $uri->getQueryAsString());
        $this->assertEquals(array('keyA' => 'valueA'), $uri->getQueryAsArray());
        $this->assertEquals('heading-1', $uri->getFragment());
        $this->assertEquals('https://example.org/another/subpath?keyA=valueA#heading-1', $uri->get());
    }
    
    /**
     * Tests the getters and setters with full constructor arguments.
     *
     */
    public function testGettersAndSettersFullCtorArgs()
    {
        $queryArray = array(
            'keyA' => 'valueA',
            'keyB' => 'valueB',
            'keyC' => 3
        );
        
        $uri = new HTTPURI(
            'https',
            'example.co.uk',
            '/path/subpath',
            $queryArray,
            'fragment'
        );
        
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('example.co.uk', $uri->getAuthority());
        $this->assertEquals('/path/subpath', $uri->getPath());
        $this->assertEquals('keyA=valueA&keyB=valueB&keyC=3', $uri->getQueryAsString());
        $this->assertEquals($queryArray, $uri->getQueryAsArray());
        $this->assertEquals('fragment', $uri->getFragment());
        $this->assertEquals('https://example.co.uk/path/subpath?keyA=valueA&keyB=valueB&keyC=3#fragment', $uri->get());
        
        $uri->setScheme('http');
        $uri->setAuthority('example.net');
        $uri->setPath('/just/another/subpath');
        $uri->setQuery(array('keyA' => 'valueA'));
        $uri->setFragment('heading-1');
        
        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('example.net', $uri->getAuthority());
        $this->assertEquals('/just/another/subpath', $uri->getPath());
        $this->assertEquals('keyA=valueA', $uri->getQueryAsString());
        $this->assertEquals(array('keyA' => 'valueA'), $uri->getQueryAsArray());
        $this->assertEquals('heading-1', $uri->getFragment());
    }
    
    /**
     * Test the getters and setters with unicode (UTF-8) arguments.
     *
     */
    public function testUnicodeGettersAndSetters()
    {
        $queryArray = array(
            'wört' => 'schlossbrücker',
            'café' => 'sûr=!',
            '←ĊÃÂşŝőÐΞφ❡⠿' => '㈱グカ゚ㄤㄦㄜㄠ',
            'a̢̱̠̼̐͊͋͗ͤ͑͘͝͡' => 'b̜̭̞̱̲̰̋̌̿͘'
        );
        
        $queryAsString = 'wört=schlossbrücker&café=sûr=!&←ĊÃÂşŝőÐΞφ❡⠿=㈱グカ゚ㄤㄦㄜㄠ&a̢̱̠̼̐͊͋͗ͤ͑͘͝͡=b̜̭̞̱̲̰̋̌̿͘';
        $path = 'b̜̭̞̱̲̰̋̌̿͘/őÐΞφ/㈱グカ゚ㄤ/café';
        $fragment = 'jdf̸̪̫̫̮͐̽̓͂dЄӦӥӢՊ';
        
        $uri = new HTTPURI(
            'https',
            'example.net',
            $path,
            $queryArray,
            $fragment
        );
        
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('example.net', $uri->getAuthority());
        $this->assertEquals('/' . $path, $uri->getPath());
        $this->assertEquals($queryArray, $uri->getQueryAsArray());
        $this->assertEquals($queryAsString, urldecode($uri->getQueryAsString()));
        $fullURI = urldecode($uri->get());
        $this->assertEquals($fullURI, "https://example.net/{$path}?{$queryAsString}#{$fragment}");
        
        $uri->setPath('グカ゚café/ŝőa̢̱̠̼̐͊͋͗ͤ͑͘͝͡ÐΞ/←ĊÃ/');
        $uri->setQuery(array('1' => 'jdf̸̪̫̫̮͐̽̓͂dЄӦӥӢ'));
        $uri->setFragment('schlossbrücker');
        
        $this->assertEquals('/グカ゚café/ŝőa̢̱̠̼̐͊͋͗ͤ͑͘͝͡ÐΞ/←ĊÃ/', $uri->getPath());
        $this->assertEquals(array('1' => 'jdf̸̪̫̫̮͐̽̓͂dЄӦӥӢ'), $uri->getQueryAsArray());
        $this->assertEquals('1=jdf̸̪̫̫̮͐̽̓͂dЄӦӥӢ', urldecode($uri->getQueryAsString()));
        $this->assertEquals('schlossbrücker', $uri->getFragment());
        
        $uri->setPath('/%E3%82%B0%E3%82%AB%E3%82%9Acaf%C3%A9/%E2%86%90%C4%8A%C3%83/', FALSE);
        $uri->setFragment('f%C3%A4hrrader', FALSE);
        $this->assertEquals('/グカ゚café/←ĊÃ/', $uri->getPath());
        $this->assertEquals('fährrader', $uri->getFragment());
    }
    
    /**
     * Test path operation methods.
     *
     */
    public function testPathOperations()
    {
        $uri = new HTTPURI(
            'http',
            'example.net',
            'path/'
        );
        
        $uri->appendPath('subpath');
        $this->assertEquals('/path/subpath', $uri->getPath());
        $uri->setPath('path/');
        $uri->appendPath('/subpath/');
        $this->assertEquals('/path/subpath/', $uri->getPath());
        $uri->setPath('/path/');
        $uri->prependPath('root');
        $this->assertEquals('/root/path/', $uri->getPath());
        $uri->prependPath('/root');
        $uri->prependPath('/');
        $uri->appendPath('/');
        $this->assertEquals('/root/root/path/', $uri->getPath());
        $this->assertEquals('/root/root/path/', $uri->getPathWithoutBase('roo'));
        $this->assertEquals('/root/root/path/', $uri->getPathWithoutBase('foo/bar'));
        $this->assertEquals('/', $uri->getPathWithoutBase('/root/root/path/'));
        $this->assertEquals('/root/path/', $uri->getPathWithoutBase('root'));
        $this->assertEquals('/path/', $uri->getPathWithoutBase('root/root/'));
        $this->assertEquals('/path/', $uri->getPathWithoutBase('/root/root/'));
    }
    
    /**
     * Test path operation methods using unicode (UTF-8).
     *
     */
    public function testPathOperationsUnicode()
    {
        $uri = new HTTPURI(
            'http',
            'example.net',
            '/'
        );
        
        $uri->appendPath('mädchen');
        $this->assertEquals('/mädchen', $uri->getPath());
        $uri->appendPath('/Զիթהתdઅ/');
        $this->assertEquals('/mädchen/Զիթהתdઅ/', $uri->getPath());
        $uri->appendPath('/k%C3%B6nnen/', FALSE);
        $this->assertEquals('/mädchen/Զիթהתdઅ/können/', $uri->getPath());
        $uri->setPath('sûr!');
        $uri->prependPath('%E3%88%B1%E3%82%B0%E3%82%AB%E3%82%9A%E3%84%A4/', FALSE);
        $uri->prependPath('');
        $this->assertEquals('/㈱グカ゚ㄤ/sûr!', $uri->getPath());
        $uri->prependPath('/f̸̪̫̫̮͐̽̓͂d/');
        $uri->prependPath('/');
        $uri->appendPath('/');
        $this->assertEquals('/f̸̪̫̫̮͐̽̓͂d/㈱グカ゚ㄤ/sûr!/', $uri->getPath());
        $this->assertEquals('/f̸̪̫̫̮͐̽̓͂d/㈱グカ゚ㄤ/sûr!/', $uri->getPathWithoutBase(''));
        $this->assertEquals('/f̸̪̫̫̮͐̽̓͂d/㈱グカ゚ㄤ/sûr!/', $uri->getPathWithoutBase('bar/baz/'));
        $this->assertEquals('/', $uri->getPathWithoutBase('/f̸̪̫̫̮͐̽̓͂d/㈱グカ゚ㄤ/sûr!/'));
        $this->assertEquals('/㈱グカ゚ㄤ/sûr!/', $uri->getpathWithoutBase('f̸̪̫̫̮͐̽̓͂d'));
        $this->assertEquals('/㈱グカ゚ㄤ/sûr!/', $uri->getpathWithoutBase('f%CC%AA%CC%AB%CD%90%CC%B8%CC%AB%CC%AE%CC%BD%CD%83%CD%82d', FALSE));
        $this->assertEquals('/sûr!/', $uri->getPathWithoutBase('f̸̪̫̫̮͐̽̓͂d/㈱グカ゚ㄤ/'));
        $this->assertEquals('/sûr!/', $uri->getPathWithoutBase('/f̸̪̫̫̮͐̽̓͂d/㈱グカ゚ㄤ/'));
    }
    
    /**
     * Test query operation methods.
     *
     */
    public function testQueryOperations()
    {
        $uri = new HTTPURI(
            'http',
            'example.net',
            '/'
        );
        
        $uri->setQuery(array('keyA' => 'valueA'));
        $uri->mergeQuery(array('keyB' => 'foo', 'keyA' => 'bar', 'keyC' => 'baz'));
        $this->assertEquals(array('keyB' => 'foo', 'keyA' => 'bar', 'keyC' => 'baz'), $uri->getQueryAsArray());
        $uri->removeQuery(array('keyC', 'keyA', 'keyD'));
        $this->assertEquals(array('keyB' => 'foo'), $uri->getQueryAsArray());
    }
    
    /**
     * Test query operation methods using unicode (UTF-8).
     *
     */
    public function testQueryOperationsUnicode()
    {
        $uri = new HTTPURI(
            'http',
            'example.net',
            '/'
        );
        
        $uri->setQuery(array('wört' => 'bücher'));
        $uri->mergeQuery(array('sûr!' => 'ÃÂşŝ', 'wört' => 'glück', '㈱グカ゚ㄤ' => 'a̢̱̠̼̐͊͋͗ͤ͑͘͝͡=b̜̭̞̱̲̰̋̌̿͘'));
        $this->assertEquals(array('sûr!' => 'ÃÂşŝ', 'wört' => 'glück', '㈱グカ゚ㄤ' => 'a̢̱̠̼̐͊͋͗ͤ͑͘͝͡=b̜̭̞̱̲̰̋̌̿͘'), $uri->getQueryAsArray());
        $uri->removeQuery(array('sûr!', 'wört', 'b̜̭̞̱̲̰̋̌̿͘'));
        $this->assertEquals(array('㈱グカ゚ㄤ' => 'a̢̱̠̼̐͊͋͗ͤ͑͘͝͡=b̜̭̞̱̲̰̋̌̿͘'), $uri->getQueryAsArray());
    }
    
    /**
     * Test pattern matching methods.
     *
     */
    public function testPathPatternMatching()
    {
        $uri = new HTTPURI(
            'https',
            'example.org',
            '/path/subpath/4446/more-subpath'
        );
        
        $this->assertEquals(FALSE, $uri->pathMatches('/wrong\\/path/'));
        $this->assertEquals(TRUE, $uri->pathMatches('/^\\/path\\/subpath/'));
        $this->assertEquals(FALSE, $uri->pathMatches('/^\\/path\\/subpath/', 'path'));
        $this->assertEquals(TRUE, $uri->pathMatches('/^\\/subpath/', 'path'));
    }
    
    /**
     * Test pattern matching methods using unicode.
     *
     */
    public function testPathPatternMatchingUnicode()
    {
        $uri = new HTTPURI(
            'https',
            'example.org',
            '/sûr!/㈱a̢̱̠̼̐͊͋͗ͤ͑͘͝͡カ゚ㄤ/glück'
        );
        
        $this->assertEquals(FALSE, $uri->pathMatches('/^mädchen/u'));
        $this->assertEquals(TRUE, $uri->pathMatches('/^\\/sûr/u'));
        $this->assertEquals(FALSE, $uri->pathMatches('/^\\/!/u', 'sûr'));
        $this->assertEquals(TRUE, $uri->pathMatches('/^\\/㈱a̢̱̠̼̐͊͋͗ͤ͑͘͝͡カ゚ㄤ\\/glück$/u', 'sûr!'));
    }
}