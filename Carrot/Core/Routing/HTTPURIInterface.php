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
 * HTTP URI Interface.
 * 
 * This interface defines the contract between HTTPURI
 * implementations and Carrot's core Router. Since HTTPURI is
 * just used to represent the request URI and base URI in
 * routing, in most cases there shouldn't be a need to replace
 * it. However, you might want to do so if you are thinking of
 * building your own route classes or if you wanted to accept
 * other encodings in your URI.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Routing;

interface HTTPURIInterface
{
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
    public function __construct($scheme, $authority, $path, $query = array(), $fragment = '');
    
    /**
     * Set the scheme part of the URI.
     * 
     * The scheme part of a HTTP URI must not be empty, see RFC 2616,
     * Section 3.2, it can be either 'http', or 'https'.
     * 
     * @param string $scheme The scheme part of the URI.
     *
     */
    public function setScheme($scheme);
    
    /**
     * Set the authority part of the URI.
     *
     * The authority part of a HTTP URI must not be empty, see RFC
     * 2616, Section 3.2, it can be either 'http', or 'https'.
     * 
     * @param string $authority The authority part of the URI.
     *
     */
    public function setAuthority($authority);
    
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
    public function setPath($path, $pathIsAlreadyDecoded = TRUE);
    
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
    public function appendPath($pathToBeAppended, $pathIsAlreadyDecoded = TRUE);
    
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
    public function prependPath($pathToBePrepended, $pathIsAlreadyDecoded = TRUE);
    
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
    public function setQuery(array $query);
    
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
    public function mergeQuery(array $queryToBeAppended);
    
    /**
     * Remove query items whose keys are listed in the given array.
     *
     * @param array $queryKeys Numerical array containing the keys
     *        of query items to be removed.
     *
     */
    public function removeQuery(array $queryKeys);
    
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
    public function setFragment($fragment, $fragmentIsAlreadyDecoded);
    
    /**
     * Get the scheme part of the URI.
     *
     * @return string
     *
     */
    public function getScheme();
    
    /**
     * Get the authority part of the URI.
     *
     * @return string
     *
     */
    public function getAuthority();
    
    /**
     * Get the path part of the URI.
     * 
     * The path string returned will have each of its segments
     * decoded from their percent encoding parts.
     * 
     * @return string
     *
     */
    public function getPath();
    
    /**
     * Get the path part of the URI, properly percent encoded.
     * 
     * This method is useful if you wanted to get a relative URI
     * string.
     * 
     * @return string
     *
     */
    public function getPathEncoded();
    
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
    public function getPathWithoutBase($basePath, $basePathIsAlreadyDecoded = TRUE);
    
    /**
     * Get the query part of the URI as percent encoded strings.
     *
     * Uses PHP native function http_build_query() on the query
     * array.
     *
     * @return string
     *
     */
    public function getQueryAsString();
    
    /**
     * Get the query part of the URI as arrays.
     * 
     * The query array returned will have its keys and values
     * decoded from percent encodings, safe for manipulation.
     * 
     * @return array
     *
     */
    public function getQueryAsArray();
    
    /**
     * Get the fragment part of the URI.
     * 
     * The fragment part returned will have its percent encodings
     * decoded.
     * 
     * @return string
     *
     */
    public function getFragment();
    
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
    public function get();
    
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
    public function pathMatches($pattern, $basePath = NULL);
}