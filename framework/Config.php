<?php

/**
 * Configuration object
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 */

/**
 * Configuration object
 *
 * Object representing user configurations. It does not have a set() method
 * as it does not allow configurations to be changed at runtime. This will
 * hopefully make testing a lot easier.
 * 
 * @package		Carrot
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @copyright	2011 Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @since		0.1
 * @version		0.1
 * @todo		
 */

class Config
{
	/**
	 * @var array Configurations stored as associative index array.
	 */
	protected $config = array();
	
	/**
	 * @var array List of configuration index name that must not be empty.
	 */
	protected $required = array();
	
	/**
	 * @var array List of configuration item defaults.
	 */
	protected $default = array();
	
	/**
	 * Constructs Config object.
	 *
	 * Config object would check for defaults first, if a config item
	 * does not exist or empty (according to $this->is_empty()), it
	 * will be replaced by default values. It also checks that all
	 * required configuration items are present and not empty. 
	 *
	 * @see Config::is_empty()
	 * @see Config::replace_defaults_if_empty()
	 * @see Config::required_config_must_exists()
	 * @param string $config Array of user configurations.
	 * @param string $defaults Array of default values, will replace user values if they are empty or non-existent.
	 * @param string $required List of items that must not be empty.
	 *
	 */
	public function __construct(array $config, array $defaults = array(), array $required = array())
	{	
		$config = $this->replace_defaults_if_empty($config, $defaults);
		$this->required_config_must_exists($config, $required);
		
		$this->required = $required;
		$this->config = $config;
	}
	
	/**
	 * Gets an item.
	 *
	 * @param string $name Item name to be fetched.
	 *
	 */
	public function item($name)
	{
		return $this->config[$name];
	}
	
	/**
	 * Gets the item. If it doesn't exist, return a default value.
	 *
	 * @param string $name Item name to be fetched.
	 * @param mixed $default Default value, to be returned if config item does not exist.
	 *
	 */
	public function get_item_or_default($name, $default)
	{
		if (isset($this->config[$name]))
		{
			return $this->config[$name];
		}
		
		return $default;
	}
	
	// ---------------------------------------------------------------
	
	/**
	 * Replace configuration items with defaults if they are empty or non-existent.
	 *
	 * @param array $config Configuration array to be replaced.
	 * @param array $defaults List of default values.
	 * @return array Configuration array with defaults inserted appropriately.
	 *
	 */
	protected function replace_defaults_if_empty(array $config, array $defaults)
	{
		foreach ($defaults as $index => $content)
		{
			if (!array_key_exists($index, $config) or $this->is_empty($config[$index]))
			{
				$config[$index] = $content;
			}
		}
		
		return $config;
	}
	
	/**
	 * Throws an exception if required configuration is empty.
	 *
	 * Loops through the list of required item names and makes sure that it
	 * exists $config and is not empty (according to $this->is_empty()).
	 * 
	 * @see Config::is_empty()
	 * @param array $config Configuration array to be checked.
	 * @param array $required List of required item names.
	 * @throws RuntimeException
	 *
	 */
	protected function required_config_must_exists(array $config, array $required)
	{
		foreach ($required as $item_name)
		{
			if (!array_key_exists($item_name, $config) or $this->is_empty($config[$item_name]))
			{
				throw new RuntimeException("Configuration file error. Required configuration ({$item_name}) is empty.");
				break;
			}
		}
	}
	
	/**
	 * Checks if a configuration item is empty.
	 *
	 * We can't use empty() to determine whether or not a configuration item
	 * is empty because a zero string '0' must also be considered a value.
	 * If the $item is an array, it is checked with empty(), if it's an integer,
	 * float, bool, or object, it is always considered not empty. If it is string,
	 * as long as it is not an empty string, it's considered not empty.
	 *
	 * @param mixed $item Contents of the configuration item to check for emptiness.
	 * @return bool TRUE if empty, FALSE if otherwise.
	 *
	 */
	protected function is_empty($item)
	{	
		if ($item === NULL)
		{
			return FALSE;
		}
		
		if (is_array($item))
		{
			return empty($item);
		}
		
		if (is_string($item))
		{
			return !isset($item{0});
		}
		
		if (is_integer($item) or is_float($item) or is_bool($item) or is_object($item))
		{
			return TRUE;
		}
		
		return FALSE;
	}
}