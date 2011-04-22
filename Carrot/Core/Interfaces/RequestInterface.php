<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Request Interface
 * 
 * This interface represents the contract between Request and Carrot's
 * default Router. If you want to replace Carrot's Request with your
 * own class, take a look at the Request class' documentation to study
 * its responsibilities to the Router.
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Interfaces;

interface RequestInterface
{	
	/**
	 * Returns wrapped $_SERVER variable.
	 *
	 * @param string $index Leave empty to return the whole array.
	 * @return mixed
	 *
	 */
	public function getServer($index = '');
	
	/**
	 * Returns wrapped $_GET variable.
	 *
	 * @param string $index Leave empty to return the whole array.
	 * @return mixed
	 *
	 */
	public function getGet($index = '');
	
	/**
	 * Returns wrapped $_POST variable.
	 *
	 * @param string $index Leave empty to return the whole array.
	 * @return mixed
	 *
	 */
	public function getPost($index = '');
	
	/**
	 * Returns wrapped $_FILES variable.
	 *
	 * @param string $index Leave empty to return the whole array.
	 * @return mixed
	 *
	 */
	public function getFiles($index = '');
	
	/**
	 * Returns wrapped $_COOKIE variable.
	 *
	 * @param string $index Leave empty to return the whole array.
	 * @return mixed
	 *
	 */
	public function getCookie($index = '');
	
	/**
	 * Returns wrapped $_REQUEST variable.
	 *
	 * @param string $index Leave empty to return the whole array.
	 * @return mixed
	 *
	 */
	public function getRequest($index = '');
	
	/**
	 * Returns wrapped $_ENV variable.
	 *
	 * @param string $index Leave empty to return the whole array.
	 * @return mixed
	 *
	 */
	public function getEnv($index = '');
	
	// ---------------------------------------------------------------
	
	/**
	 * Returns base path (with starting and trailing slash).
	 *
	 * Base path is the relative path from server root to the folder
	 * where the front controller is located. If the front controller
	 * is in the server root, it simply returns '/'.
	 *
	 * @return string Base path.
	 *
	 */
	public function getBasePath();
	
	/**
	 * Returns the application URI segments (array).
	 *
	 * Application request URI is different from Request URI in that it doesn't
	 * include the base path. So if your base path is '/base/path/' and
	 * your the request uri is '/base/path/item/id', the application request
	 * URI will be:
	 *
	 * <code>
	 * array('item', 'id')
	 * </code>
	 *
	 * @return array Application request URI in segments.
	 *
	 */
	public function getAppRequestURISegments();
	
	// ---------------------------------------------------------------
	
	/**
	 * Wrapper for PHP isset on $_SERVER index.
	 *
	 * @param string $index The index to check.
	 * @return bool TRUE if the index exists, FALSE otherwise.
	 *
	 */
	public function issetServer($index);
	
	/**
	 * Wrapper for PHP isset on $_GET index.
	 *
	 * @param string $index The index to check.
	 * @return bool TRUE if the index exists, FALSE otherwise.
	 *
	 */
	public function issetGet($index);
	
	/**
	 * Wrapper for PHP isset on $_POST index.
	 *
	 * @param string $index The index to check.
	 * @return bool TRUE if the index exists, FALSE otherwise.
	 *
	 */
	public function issetPost($index);
	
	/**
	 * Wrapper for PHP isset on $_FILES index.
	 *
	 * @param string $index The index to check.
	 * @return bool TRUE if the index exists, FALSE otherwise.
	 *
	 */
	public function issetFiles($index);
	
	/**
	 * Wrapper for PHP isset on $_COOKIE index.
	 *
	 * @param string $index The index to check.
	 * @return bool TRUE if the index exists, FALSE otherwise.
	 *
	 */
	public function issetCookie($index = '');
	
	/**
	 * Wrapper for PHP isset on $_REQUEST index.
	 *
	 * @param string $index The index to check.
	 * @return bool TRUE if the index exists, FALSE otherwise.
	 *
	 */
	public function issetRequest($index = '');
	
	/**
	 * Wrapper for PHP isset on $_ENV index.
	 *
	 * @param string $index The index to check.
	 * @return bool TRUE if the index exists, FALSE otherwise.
	 *
	 */
	public function issetEnv($index = '');
}