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
 * Dependency list.
 *
 * This object contains the list of object dependencies belonging
 * to a container instance. This object lists only object
 * dependencies, represented by a Reference instance. It also
 * contains instantiated dependencies, and thus hold a state of
 * whether all the dependencies are fulfilled or not.
 *
 * It is to be returned by the injector when inquired by the
 * container. The container needs the dependency list because it
 * has to build the object graph recursively. Primitive type
 * dependencies are not recursive and therefore can be resolved
 * by the injector itself.
 *
 * The container gets this object from the injector and will
 * instantiate every dependencies listed, after which it will
 * pass the instantiated dependencies back to this object via
 * {@see setInstantiatedDependency()}. When all dependencies are
 * fulfilled the container can then call the appropriate injector
 * method to instantiate the needed class.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection;

use InvalidArgumentException;

class DependencyList
{
    /**
     * @var array List of dependencies represented by Reference instances.
     */
    protected $list = array();
    
    /**
     * @var array List of dependencies, instantiated properly.
     */
    protected $instantiatedDependencies = array();
    
    /**
     * Constructor.
     *
     * A dependency in the list is represented by a Reference
     * instance, which refers to the dependency's object instance.
     * You must pass an array containing instances of Reference at
     * construction:
     *
     * <code>
     * $list = new DependencyList(array(
     *     $referenceA,
     *     $referenceB,
     *     $referenceC,
     *     ...
     * ));
     * </code>
     *
     * Throws InvalidArgumentException if the array provided contains
     * something other than Reference instances.
     * 
     * @throws InvalidArgumentException
     * @param array $list Contains instances of Reference.
     *
     */
    public function __construct(array $list)
    {
        foreach ($list as $reference)
        {
            if ($reference instanceof Reference == FALSE)
            {
                $unexpectedType = is_object($reference) ? get_class($reference) : gettype($reference);
                throw new InvalidArgumentException("DependencyList error in instantiation. The list provided must contain only instances of Carrot\Core\DependencyInjection\Reference, '{$unexpectedType}' found.");
            }
            
            $this->list[$reference->getID()] = $reference;
        }
    }
    
    /**
     * Get the list of dependencies.
     *
     * Returns an array containing instances of Reference, which
     * refers to the dependencies.
     *
     * The returned array will have the instance ID as the index and
     * a Reference instance as the content. Example returned array
     * structure:
     *
     * <code>
     * $list = array(
     *     'Carrot\Core\Request{Singleton:}' => $referenceB,
     *     'Acme\App\Controller{Singleton:Main}' => $referenceA,
     *     ...
     * );
     * </code>
     *
     * @return array
     *
     */
    public function getList()
    {
        return $this->list;
    }
    
    /**
     * Set an instantiated dependency.
     *
     * Will first check if the Reference instance provided is
     * actually registered in the list, will throw
     * InvalidArgumentException if not.
     *
     * Will also check if the provided dependency is the actual
     * required dependency object. If not, will also throw an
     * InvalidArgumentException.
     * 
     * @throws InvalidArgumentException
     * @param Reference $reference Refers to the instantiated dependency object instance.
     * @param mixed $dependency The instantiated dependency object.
     * 
     */
    public function setInstantiatedDependency(Reference $reference, $dependency)
    {
        $id = $reference->getID();
        $class = $reference->getClass();
        
        if (array_key_exists($id, $this->list) == FALSE)
        {
            throw new InvalidArgumentException("DependencyList error in setting instantiated dependency. The Reference instance provided ({$id}) is not present in the dependency list.");
        }
        
        if ($dependency instanceof $class == FALSE)
        {
            $unexpectedType = is_object($dependency) ? get_class($dependency) : gettype($dependency);
            throw new InvalidArgumentException("DependencyList error in setting instantiated dependency. {$class} was expected, {$unexpectedType} given.");
        }
        
        $this->instantiatedDependencies[$id] = $dependency;
    }
    
    /**
     * Returns an array containing instantiated dependencies.
     *
     * Before getting instantiated dependencies, first check if all
     * dependencies are fulfilled using
     * {@see areAllDependenciesFulfilled()}.
     *
     * The array returned will have instance ID as the index and the
     * instantiated dependencies as the content. Example returned
     * array structure:
     *
     * <code>
     * $instantiatedDependencies = array(
     *     'Carrot\Core\Request{Singleton:}' => $request,
     *     'Acme\App\Controller{Singleton:Main}' => $controller,
     *     ...
     * );
     * </code>
     *
     * @return array 
     *
     */
    public function getInstantiatedDependencies()
    {
        return $this->instantiatedDependencies;
    }
    
    /**
     * Get an instantiated dependency from the given Reference instance.
     * 
     * Throws InvalidArgumentException if the Reference instance
     * given is not present in the dependency list.
     * 
     * @throws InvalidArgumentException
     * @param Reference $reference Refers to the instantiated dependency we wanted to get.
     * @return mixed|FALSE The instantiated dependency, or FALSE if it's not yet present.
     *
     */
    public function getInstantiatedDependency(Reference $reference)
    {
        $id = $reference->getID();
        
        if (array_key_exists($id, $this->list) == FALSE)
        {
            throw new InvalidArgumentException("DependencyList error in getting an instantiated dependency. The Reference instance provided ({$id}) is not present in the dependency list.");
        }
        
        if (array_key_exists($id, $this->instantiatedDependencies) == FALSE)
        {
            return FALSE;
        }
        
        return $this->instantiatedDependencies[$id];
    }
    
    /**
     * Check if all instantiated dependencies has been provided to
     * this object.
     * 
     * All dependencies are considered fulfilled if the count number
     * of {@see $instantiatedDependencies} class property is the
     * same as the count of {@see $list} class property.
     * 
     * @return bool TRUE if all dependencies are fulfilled, FALSE otherwise.
     *
     */
    public function areAllDependenciesFulfilled()
    {
        return (count($this->list) === count($this->instantiatedDependencies));
    }
    
    /**
     * Checks if this object is identical to the given DependencyList
     * instance.
     *
     * Two DependencyList instances are considered identical if their
     * dependency list keys (the instance IDs) matches according to
     * the PHP == array operator specification. Please note that this
     * method does not check if the two instance are really the same
     * instance. It also doesn't check the instantiated dependencies
     * on the two objects. As long as the instance IDs on the list is
     * the same, the two object is considered identical.
     *
     * @return bool TRUE if considered identical, FALSE otherwise
     *
     */
    public function isIdentical(DependencyList $dependencyList)
    {
        $listA = array_keys($dependencyList->getList());
        $listB = array_keys($this->list);
        return ($listA == $listB);
    }
}