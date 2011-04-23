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
 * Destination
 * 
 * Represents the destination, Router's responsibility is to check the routes
 * and return a valid Destination object. The front controller will read this
 * object, instantiate the controller, and run the method needed.
 *
 * @author		Ricky Christie <seven.rchristie@gmail.com>
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\Classes;

class Destination
{
	/**
	 * @var string Controller class' DIC item ID.
	 */
	protected $controller_dic_id;
	
	/**
	 * @var string Method name to be called.
	 */
	protected $method;
	
	/**
	 * @var array Array of paramters to be passed.
	 */
	protected $params;
	
	/**
	 * @var string Bundle name means the two top level namespace (\Vendor\Namespace).
	 */
	protected $bundle_name;
	
	/**
	 * @var string The name of the controller class derived from the DIC ID.
	 */
	protected $class_name;
	
	/**
	 * Creates a Destination object.
	 *
	 * Will throw an exception if the controller DIC item ID doesn't
	 * pass validation process. Example usage:
	 *
	 * <code>
	 * $destination = new Destination
	 * (
	 *     '\Vendor\Namespace\Subnamespace\BlogController:main',
	 *     'index',
	 *     array(5, 'Foo', 'Bar')
	 * );
	 * </code>
	 *
	 * @param string $controller_dic_id DIC item ID for the controller.
	 * @param string $method Method name to call.
	 * @param array $params Array of parameters, to be passed in sequence.
	 *
	 */
	public function __construct($controller_dic_id, $method, array $params = array())
	{
		if (!$this->validateID($controller_dic_id))
		{
			throw new \InvalidArgumentException("Error in creating Destination object, Controller DIC registration ID is not valid ({$controller_dic_id}).");
		}
		
		$this->controller_dic_id = $controller_dic_id;
		$this->method = $method;
		$this->params = $params;
		$this->class_name = $this->getClassNameFromID($controller_dic_id);
		$this->bundle_name = $this->getBundleNameFromID($controller_dic_id);
	}
	
	// ---------------------------------------------------------------
	
	/**
	 * Validates DIC configuration item ID.
	 *
	 * @param string $id DIC item ID.
	 * @return bool TRUE if valid, FALSE otherwise.
	 *
	 */
	protected function validateID($id)
	{
		$id_exploded = explode(':', $id);
		
		return
		(
			count($id_exploded) == 2 &&
			!empty($id_exploded[0]) &&
			!empty($id_exploded[1]) &&
			$id_exploded[0]{0} == '\\' &&
			substr_count($id_exploded[0], '\\') >= 2
		);
	}
	
	/**
	 * Get the fully qualified class name from DIC item ID.
	 *
	 * @param string $id DIC item ID.
	 * @return string Fully qualified class name.
	 *
	 */
	protected function getClassNameFromID($id)
	{
		$id_exploded = explode(':', $id);
		return $id_exploded[0];
	}
	
	/**
	 * Gets the bundle name (\Vendor\Namespace) from DIC item ID.
	 *
	 * Bundle name means the top level namespace (vendor) and the namespace
	 * of the class, according to PSR-0 Final Proposal. 
	 *
	 * @param string $id DIC item ID.
	 * @return string Bundle name (\Vendor\Namespace).
	 *
	 */
	protected function getBundleNameFromID($id)
	{
		$id_exploded = explode(':', $id);
		$namespaces = explode('\\', $id_exploded[0]);
		$fragment_saved = 0;
		$bundle_name = '';
		
		// Get the first two fragment
		foreach ($namespaces as $fragment)
		{
			if ($fragment_saved == 2)
			{
				break;
			}
			
			if (!empty($fragment))
			{
				$bundle_name .= '\\' . $fragment;
				$fragment_saved++;
			}
		}
		
		if ($fragment_saved != 2)
		{
			throw new \InvalidArgumentException("Error in getting bundle name from DIC configuration item, '{$id}' does not have a proper namespace.");
		}
		
		return $bundle_name;
	}
}