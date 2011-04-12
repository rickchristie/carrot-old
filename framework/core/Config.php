<?php

/**
 * The short description
 *
 * As many lines of extendend description as you want {@link element}
 * links to an element
 * {@link http://www.example.com Example hyperlink inline link} links to
 * a website. The inline
 * source tag displays function source code in the description:
 * {@source } 
 * 
 * {@link http://www.example.com Read more}
 *
 * @package			package_name
 * @subpackage		sub package name, groupings inside of a project
 * @author 		  	author name <author@email>
 * @copyright		name date
 * @deprecated	 	description
 * @param		 	type [$varname] description
 * @return		 	type description
 * @since		 	a version or a date
 * @todo			phpdoc.de compatibility
 * @var				type	a data type for a class variable
 * @version			version
 */

class Config
{
	protected $config = array();
	
	/**
	 * Constructs Config object.
	 *
	 * The constructor will load config.php file 
	 *
	 * @throws 
	 *
	 */
	public function __construct($abspath = '')
	{
		// The $abspath variable is the ABSOLUTE PATH
		// to the folder where the config.php is stored.
		// Config class will check for it's existence,
		// require and load the variables into $this->config.
		
		if (empty($abspath))
		{
			$abspath = $this->determine_abspath();
		}
		
		if (!file_exists($abspath . 'config.php'))
		{
			throw new InvalidArgumentException("Config object, error when instantiating. Path does not contain config.php ({$abspath})");
		}
		
		require($abspath . 'config.php');
		$config['abspath'] = $abspath;
		
		// Default controller must not be empty
		if (empty($config['default_request_name']))
		{
			exit('Configuration file error. Default request name must not be empty.');
		}
		
		/*
		|---------------------------------------------------------------
		| FILLING IN THE BLANKS
		|---------------------------------------------------------------
		*/
		
		// Guess protocol
		if (!isset($config['protocol']) or empty($config['protocol']))
		{
			// CREDIT: Regin Gaarsmand, http://www.sourcerally.net/regin
			$config['protocol'] = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5)) == 'https' ? 'https' : 'http';
		}
		
		// Guess domain name
		if (!isset($config['domain']) or empty($config['domain']))
		{
			$config['domain'] = $_SERVER['SERVER_NAME'];
		}
		
		// Guess path
		if (!isset($config['path']) or empty($config['path']))
		{
			// Assuming that this file is always loaded by framework.php
			// and framework.php is always loaded by index.php
			// then SCRIPT_NAME should always be {path}/index.php
			
			$config['path'] = str_ireplace('/index.php', '', $_SERVER['SCRIPT_NAME']);
			
			// Add trailing slash
			if (empty($config['path']) or substr($config['path'], -1) != '/')
			{
				$config['path'] .= '/';
			}
		}
		
		$this->config = $config;
	}
	
	/**
	 * Determines the current absolute path for the framework.
	 *
	 * Assuming that this file is location is /framework/core/Config.php,
	 * this function gets the absolute path to this framework's root
	 * folder.
	 *
	 */
	public function determine_abspath()
	{
		return realpath(realpath(dirname(__FILE__) . '/../..') . '/');
	}
	
	// ---------------------------------------------------------------
	
	public function item($name)
	{
		return $this->config[$name];
	}
	
	public function get_item_or_default($name, $default)
	{
		if (isset($this->config[$name]))
		{
			return $this->config[$name];
		}
		
		return $default;
	}
}