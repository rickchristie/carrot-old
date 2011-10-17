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
 * HTTP URI.
 * 
 * This class represents a HTTP URI. It doesn't try to validate
 * the URI, it just tries to parse it as best as it can according
 * to the specification. It then provides methods to inspect and
 * make changes to the URI. This class assumes that the encoding
 * for the HTTP URI data is UTF-8. This should work in most
 * cases. If you're having problems, you can replace this
 * implementation with your own by implementing HTTPURIInterface.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Routing;

use Exception,
    InvalidArgumentException;

class HTTPURI implements HTTPURIInterface
{   
    /**
     * @var string The scheme part of the URI.
     */
    protected $scheme;
    
    /**
     * @var string The authority part of the URI.
     */
    protected $authority;
    
    /**
     * @var string The path part of the URI, not percent encoded.
     */
    protected $path;
    
    /**
     * @var array The query part of the URI, assumed to be in
     *      'key=value' format, with each item's percent encoding
     *      part decoded.
     */
    protected $query;
    
    /**
     * @var string The fragment part of the URI, not percent encoded.
     */
    protected $fragment;
       
    /**
     * Constructor.
     * 
     * Example constructor arguments:
     *
     * <code>
     * $uri = new HTTPURI(
     *     'http',
     *     'www.example.com',
     *     '/path/sub/path',
     *     array(
     *         'keyA' => 'valueA',
     *         'keyB' => 'valueB'
     *     ),
     *     'fragment'
     * );
     * </code>
     * 
     * @param string $scheme The scheme part of the URL.
     * @param string $authority The authority part of the URL.
     * @param string $path The path part of the URL, still percent
     *        encoded, preferably taken from $_SERVER['REQUEST_URI'].
     * @param array $query The query variables, preferably taken from
     *        $_GET superglobal, with each keys/values decoded from
     *        their percent encodings.
     * @param string $fragment The fragment part of the URI, still
     *        percent encoded.
     *
     */
    public function __construct($scheme, $authority, $path, $query = array(), $fragment = '')
    {
        $this->setScheme($scheme);
        $this->setAuthority($authority);
        $this->setPath($path, FALSE);
        $this->setQuery($query);
        $this->setFragment($fragment, FALSE);
    }
    
    /**
     * Set the scheme part of the URI.
     * 
     * The scheme part of a HTTP URI must not be empty, see RFC 2616,
     * Section 3.2, it can be either 'http', or 'https'.
     * 
     * @param string $scheme The scheme part of the URI.
     *
     */
    public function setScheme($scheme)
    {
        if (empty($scheme))
        {
            throw new InvalidArgumentException("HTTPURI error in setting the scheme. The scheme part of the URI must not be empty.");
        }
        
        $this->scheme = $scheme;
    }
    
    /**
     * Set the authority part of the URI.
     *
     * The authority part of a HTTP URI must not be empty, see RFC
     * 2616, Section 3.2, it can be either 'http', or 'https'.
     * 
     * @param string $authority The authority part of the URI.
     *
     */
    public function setAuthority($authority)
    {
        if (empty($authority))
        {
            throw new InvalidArgumentException("HTTPURI error in setting the authority. The authority part of the URI must not be empty.");
        }
        
        $this->authority = $authority;
    }
    
    /**
     * Set the path part of the URI.
     * 
     * This method needs the given path to be already decoded from
     * any percent encodings. If the given path hasn't been decoded
     * yet, tell the method so it can help you decode it.
     * 
     * This method will add a starting slash to the path, whether
     * you like it or not. This is because for URIs that contain an
     * authority component, the path component must begin with a
     * slash character (RFC 3986, Section 3.3), and all HTTP URI must
     * have a host, which means all HTTP URI has an authority
     * component (RFC 2616, Section 3.2.2).
     * 
     * NOTE: This method will remove all string coming after the '?'
     * character ({@see removeQueryStringFromPath()}) This makes it
     * safe to use this method with $_SERVER['REQUEST_URI'] variable.
     * 
     * @param string $path The path string to set.
     * @param bool $pathIsAlreadyDecoded If FALSE, this method will
     *        decode percent encodings on each segment of the given
     *        path using urldecode().
     *
     */
    public function setPath($path, $pathIsAlreadyDecoded = TRUE)
    {
        if (empty($path))
        {
            throw new InvalidArgumentException("HTTPURI error in setting the path. The path part of the URI must not be empty. At the very least, the path must contain a slash '/' to be unambiguous in denoting the root.");
        }
        
        $path = ltrim($path, '/');
        $path = '/' . $path;
        $path = $this->removeQueryStringFromPath($path);
        
        if ($pathIsAlreadyDecoded == FALSE)
        {
            $path = $this->decodePath($path);
        }
        
        $this->path = $path;
    }
    
    /**
     * Append a path string to the path part of the URI.
     * 
     * The path string given is treated as a segment and will be
     * joined with the slash character to the original path string.
     * 
     * This method needs the given path to be already decoded from
     * any percent encodings. If the given path hasn't been decoded
     * yet, tell the method so it can help you decode it.
     * 
     * NOTE: This method will remove all string coming after the '?'
     * character ({@see removeQueryStringFromPath()}) This makes it
     * safe to use this method with $_SERVER['REQUEST_URI'] variable.
     * 
     * @param string $pathToBeAppended The path to be appended.
     * @param bool $pathIsAlreadyDecoded If FALSE, this method will
     *        decode percent encodings on each segment of the given
     *        path using urldecode().
     *
     */
    public function appendPath($pathToBeAppended, $pathIsAlreadyDecoded = TRUE)
    {
        $pathToBeAppended = $this->removeQueryStringFromPath($pathToBeAppended);
        
        if ($pathIsAlreadyDecoded == FALSE)
        {
            $pathToBeAppended = $this->decodePath($pathToBeAppended);
        }
        
        $pathToBeAppended = ltrim($pathToBeAppended, '/');
        $this->path = rtrim($this->path, '/');
        $this->path .= '/' . $pathToBeAppended;
    }
    
    /**
     * Prepend a path string to the path part of the URI.
     * 
     * The path string given is treated as a segment and will be
     * joined with the slash character to the original path string.
     * 
     * This method needs the given path to be already decoded from
     * any percent encodings. If the given path hasn't been decoded
     * yet, tell the method so it can help you decode it.
     *
     * This method will add a starting slash to the path, whether
     * you like it or not. This is because for URIs that contain an
     * authority component, the path component must begin with a
     * slash character (RFC 3986, Section 3.3), and all HTTP URI must
     * have a host, which means all HTTP URI has an authority
     * component (RFC 2616, Section 3.2.2).
     *
     * NOTE: This method will remove all string coming after the '?'
     * character ({@see removeQueryStringFromPath()}) This makes it
     * safe to use this method with $_SERVER['REQUEST_URI'] variable.
     *
     * @param string $pathToBePrepended The path to be prepended.
     * @param bool $pathIsAlreadyDecoded If FALSE, this method will
     *        decode percent encodings on each segment of the given
     *        path using urldecode().
     *
     */
    public function prependPath($pathToBePrepended, $pathIsAlreadyDecoded = TRUE)
    {
        $pathToBePrepended = $this->removeQueryStringFromPath($pathToBePrepended);
        
        if ($pathIsAlreadyDecoded == FALSE)
        {
            $pathToBePrepended = $this->decodePath($pathToBePrepended);
        }
        
        $pathToBePrepended = trim($pathToBePrepended, '/');
        
        // There's no point in prepending an empty
        // path, as starting slash is guaranteed
        // in this class.
        if ($pathToBePrepended == '')
        {
            return;
        }
        
        $this->path = ltrim($this->path, '/');
        $this->path = '/' . $pathToBePrepended . '/' . $this->path;
    }
    
    /**
     * Set the query part of the URI using array of keys and values.
     * 
     * As this is a HTTP URI class, it assumes that the query string
     * is always formatted in 'key=value' format. 
     * 
     * @param array $query Contains list of keys and values, exactly
     *        like the $_GET superglobal. All keys and values must
     *        already be decoded from their percent encoding.
     *
     */
    public function setQuery(array $query)
    {
        $this->query = $query;
    }
    
    /**
     * Append a query array.
     * 
     * The query arrays are merged according to array_merge() rules.
     * 
     * @param array $queryToBeAppended Contains list of keys and
     *        values, exactly like the $_GET superglobal. All keys
     *        and values must already have their percent encodings
     *        decoded.
     *
     */
    public function mergeQuery(array $queryToBeAppended)
    {   
        $this->query = array_merge($this->query, $queryToBeAppended);
    }
    
    /**
     * Remove query items whose keys are listed in the given array.
     *
     * @param array $queryKeys Numerical array containing the keys
     *        of query items to be removed.
     *
     */
    public function removeQuery(array $queryKeys)
    {
        foreach ($queryKeys as $key)
        {
            unset($this->query[$key]);
        }
    }
    
    /**
     * Set the fragment part of the URI.
     *
     * This method needs the given fragment to be already decoded
     * from any percent encodings. If the given fragment hasn't been
     * decoded yet, tell the method so it can help you decode it.
     *
     * @param string $fragment The fragment string.
     * @param bool $pathIsAlreadyDecoded If FALSE, this method will
     *        decode percent encodings on each segment of the given
     *        path using urldecode().
     *
     */
    public function setFragment($fragment, $fragmentIsAlreadyDecoded = TRUE)
    {
        if ($fragmentIsAlreadyDecoded == FALSE)
        {
            $fragment = urldecode($fragment);
        }
        
        $this->fragment = $fragment;
    }
    
    /**
     * Get the scheme part of the URI.
     *
     * @return string
     *
     */
    public function getScheme()
    {
        return $this->scheme;
    }
    
    /**
     * Get the authority part of the URI.
     *
     * @return string
     *
     */
    public function getAuthority()
    {
        return $this->authority;
    }
    
    /**
     * Get the path part of the URI.
     * 
     * The path string returned will have each of its segments
     * decoded from their percent encoding parts.
     * 
     * @return string
     *
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Get the path part of the URI, properly percent encoded.
     * 
     * This method is useful if you wanted to get a relative URI
     * string.
     * 
     * @return string
     *
     */
    public function getPathEncoded()
    {
        return $this->encodePath($this->path);
    }
    
    /**
     * Get the path with the given base path removed.
     *
     * This method is useful in host/location agnostic routing, where
     * you might want to eliminate the base directory from the path
     * before matching it against a pattern, therefore making the
     * routing safe and consistent even though the location of the
     * application may be changed.
     *
     * This method needs the given base path to be already decoded
     * from any percent encodings. If the given base path isn't
     * decoded yet, tell the method so it can help you decode it.
     * 
     * This method uses PCRE preg_replace() with '/u' modifier
     * instead of substr() for multibyte safety. This means that the
     * path string sent to the server the base path you use must be
     * encoded in UTF-8. Otherwise this method will fail.
     *
     * The returned path will ALWAYS have a starting slash. If the
     * base path is not found, nothing will be removed from the path.
     * The returned path will not be percent encoded.
     *
     * @param string $basePath The base path to be removed. Must be
     *        in UTF-8 encoding.
     * @param bool $basePathIsAlreadyDecoded If TRUE, this will skip
     *        the decoding of percent encodings on each segment using
     *        urldecode().
     * @return string The path without the base path.
     *
     */
    public function getPathWithoutBase($basePath, $basePathIsAlreadyDecoded = TRUE)
    {
        if ($this->path == '')
        {
            return '';
        }
        
        if ($basePathIsAlreadyDecoded == FALSE)
        {
            $basePath = $this->decodePath($basePath);
        }
        
        // Base path always have starting
        // and trailing slash.
        $basePath = trim($basePath, '/');
        $basePath = '/' . $basePath . '/';
        $basePath = $this->escapePCREMetaCharacters($basePath);
        $path = $this->PCREReplace("/^{$basePath}/u", '', $this->path);
        
        // Ensures that the returned path always
        // starts with a starting slash before
        // returning.
        $path = ltrim($path, '/');
        $path = '/' . $path;
        return $path;
    }
    
    /**
     * Get the query part of the URI as percent encoded strings.
     *
     * Uses PHP native function http_build_query() on the query
     * array.
     *
     * @return string
     *
     */
    public function getQueryAsString()
    {
        return http_build_query($this->query);
    }
    
    /**
     * Get the query part of the URI as arrays.
     * 
     * The query array returned will have its keys and values
     * decoded from percent encodings, safe for manipulation.
     * 
     * @return array
     *
     */
    public function getQueryAsArray()
    {
        return $this->query;
    }
    
    /**
     * Get the fragment part of the URI.
     * 
     * The fragment part returned will have its percent encodings
     * decoded.
     * 
     * @return string
     *
     */
    public function getFragment()
    {
        return $this->fragment;
    }
    
    /**
     * Get the whole URI string, with each part properly percent
     * encoded.
     * 
     * Here is percent-encoding rule:
     * 
     * <ul>
     *     <li>
     *         The path part of the URI will have each of its
     *         segments percent encoded.
     *     </li>
     *     <li>
     *         The query part of the URI will be percent encoded
     *         using http_build_query().
     *     </li>
     *     <li>
     *         Scheme and authority will not changed at all. This is
     *         because inconsistent client behavior when using
     *         percent encoding in domain names. Most clients will
     *         use transform multibyte URI into punycode anyway, so
     *         it's best to leave the authority untouched.
     *     </li>
     * </ul>
     * 
     * Although this method percent encodes the URI string, it still
     * might not be safe for HTML output. You can use htmlentities()
     * or htmlspecialchars() to encode special HTML characters into
     * HTML entities.
     * 
     * @return string
     *
     */
    public function get()
    {
        $scheme = '';
        $authority = '';
        $path = $this->encodePath($this->path);
        $query = $this->getQueryAsString();
        $fragment = urlencode($this->fragment);
        
        if ($this->scheme != '')
        {
            $scheme = $this->scheme . '://';
        }
        
        if ($this->authority != '')
        {
            $authority = $this->authority;
            $path = ltrim($path, '/');
            $path = '/' . $path;
        }
        
        if ($query != '')
        {
            $query = '?' . $query;
        }
        
        if ($fragment != '')
        {
            $fragment = '#' . $fragment;
        }
        
        return $scheme . $authority . $path . $query . $fragment;
    }
    
    /**
     * Check if the path part of this URI (decoded) matches the given
     * regular expression.
     *
     * If the $basePath argument is not NULL, this method will run
     * {@see getPathWithoutBase()} and use the return value from
     * that method as the path string to be matched instead.
     *
     * NOTE: Don't forget to use the '/u' modifier in your pattern!
     * Also make sure your pattern is valid before entering, since
     * this method will catch exceptions and interpret it as a
     * failure in matching, returning FALSE.
     * 
     * @param string $pattern The regular expression to match this
     *        URI's path.
     * @param string|NULL $basePath The base path to be removed
     *        before running the regex (if any). Must already been
     *        decoded from any percent encodings.
     * @return bool TRUE if found, FALSE otherwise.
     *
     */
    public function pathMatches($pattern, $basePath = NULL)
    {
        $path = $this->path;
        
        if ($basePath != NULL)
        {
            $path = $this->getPathWithoutBase($basePath, TRUE);
        }
        
        try
        {
            $found = preg_match($pattern, $path);
        }
        catch (Exception $exception)
        {
            // Cannot use preg_match(), most likely
            // there's an error in the string encodings.
            return FALSE;
        }
        
        return (bool) $found;
    }
    
    /**
     * Explode the given path into segments and decodes percent
     * encoding of each segment using urldecode().
     *
     * @param string $path The path to be decoded.
     *
     */
    protected function decodePath($path)
    {
        $explodedPath = explode('/', $path);
        $decodedPath = array();
        
        foreach ($explodedPath as $segment)
        {
            $decodedPath[] = urldecode($segment);
        }
        
        return implode('/', $decodedPath);
    }
    
    /**
     * Explode the given path into segments and encodes each segment
     * using urlencode().
     *
     * @param string $path The path to be encoded.
     *
     */
    protected function encodePath($path)
    {
        $explodedPath = explode('/', $path);
        $encodedPath = array();
        
        foreach ($explodedPath as $segment)
        {
            $encodedPath[] = urlencode($segment);
        }
        
        return implode('/', $encodedPath);
    }
    
    /**
     * Removes any string after the question mark '?' or hash '#'
     * using preg_replace() for UTF-8 multibyte safety.
     *
     * @param string $path
     * @return string The path, with query string removed.
     *
     */
    protected function removeQueryStringFromPath($path)
    {
        return $this->PCREReplace('/\\?.*$/uD', '', $path);
    }
    
    /**
     * Escape all PCRE metacharacters on the given string.
     * 
     * This method Replaces all PCRE meta characters as listed in
     * {@see http://www.php.net/manual/en/regexp.reference.meta.php}
     * to its escaped counterpart. It also replaces the slash '/'
     * character since it is used as delimiters.
     * 
     * The reason this method exist is that we have to sidestep the
     * usage of non-multibyte-safe string functions like substr()
     * by using preg_* methods with '/u' modifier, which treats the
     * string as UTF-8. By sidestepping substr() method, multibyte
     * URIs that are encoded with UTF-8 should still work in this
     * class even without multibyte extension turned on.
     * 
     * @param string $string The string to be escaped.
     * @return string The escaped string if it's successful, or the
     *         original string if it fails.
     * 
     */
    protected function escapePCREMetaCharacters($string)
    {
        $patterns = array(
            '/\\//u', // Search for '/'
            '/\\\\/u', // Search for '\'
            '/\\^/u', // Search for '^'
            '/\\$/u', // Search for '$'
            '/\\./u', // Search for '.'
            '/\\[/u', // Search for '['
            '/\\]/u', // Search for ']'
            '/\\|/u', // Search for '|'
            '/\\(/u', // Search for '('
            '/\\(/u', // Search for ')'
            '/\\*/u', // Search for '*'
            '/\\+/u', // Search for '+'
            '/\\{/u', // Search for '{'
            '/\\}/u', // Search for '}'
            '/\\-/u', // Search for '-'
        );
        
        $replacements = array(
            '\\/', // Replaces '/'
            '\\\\', // Replaces '\'
            '\\^', // Replaces '^'
            '\\$', // Replaces '$'
            '\\.', // Replaces '.'
            '\\[', // Replaces '['
            '\\]', // Replaces ']'
            '\\|', // Replaces '|'
            '\\(', // Replaces '('
            '\\)', // Replaces ')'
            '\\*', // Replaces '*'
            '\\+', // Replaces '+'
            '\\{', // Replaces '{'
            '\\}', // Replaces '}'
            '\\-', // Replaces '-'
        );
        
        return $this->PCREReplace($patterns, $replacements, $string);
    }
    
    /**
     * Run the preg_replace() function with the given arguments, but
     * wraps it in try catch block.
     * 
     * Either return the original subject (because of no match or
     * error) or return the replaced subject.
     *
     * @param string $pattern As in preg_replace().
     * @param string $replacement As in preg_replace().
     * @param string $subject As in preg_replace().
     * @param int $limit As in preg_replace().
     * @return string
     *
     */
    protected function PCREReplace($pattern, $replacement, $subject, $limit = -1)
    {
        try
        {
            $replaced = preg_replace($pattern, $replacement, $subject, $limit);
        }
        catch(Exception $exception)
        {
            // Failed to run the replace method, most likely
            // the path string is not in a safe format for 
            // preg_replace().
            return $subject;
        }
        
        if ($replaced === NULL)
        {
            return $subject;
        }
        
        return $replaced;
    }
}