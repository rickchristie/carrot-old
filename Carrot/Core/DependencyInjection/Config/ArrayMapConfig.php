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
 * Array map injection configuration.
 *
 * This object reads configuration in static array form and
 * converts it to ConstructorInjector, ProviderInjector, and
 * CallbackInjector instances. Since the configuration is static,
 * you will have to wire dependencies manually for each class you
 * need by writing their configuration array. This may not be
 * convenient, but since it's static it should be faster than
 * BindingsConfig.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\DependencyInjection\Config;

use RuntimeException,
    Carrot\Core\DependencyInjection\Reference,
    Carrot\Core\DependencyInjection\Injector\InjectorInterface,
    Carrot\Core\DependencyInjection\Injector\ConstructorInjector,
    Carrot\Core\DependencyInjection\Injector\ProviderInjector,
    Carrot\Core\DependencyInjection\Injector\CallbackInjector;

class ArrayMapConfig implements ConfigInterface
{
    /**
     * @var array List of explicitly set injectors.
     */
    protected $injectors = array();
    
    /**
     * @var array Configuration array.
     */
    protected $config;
    
    /**
     * Constructor.
     *
     * The configuration array must follow a specific structure,
     * otherwise it will be ignored by this class. This object can
     * only transform array configuration to instances of
     * ConstructorInjector, ProviderInjector, and CallbackInjector.
     * However, if you wish to use other injector instances, you can
     * do so using the default {@see setInjector()} method.
     *
     * Pass an array containing the list of configuration item arrays
     * when instantiating this object. The array's index must be the
     * instance ID of the configuration item.
     *
     * <code>
     * $config = new ArrayMapConfig(
     *     'Acme\App\Controller{Singleton:}' => $itemA,
     *     'Carrot\Database\MySQLiWrapper\MySQLi{Singleton:}' => $itemB,
     *     'Acme\App\Model{Singleton:}' => $itemC
     * );
     * </code>
     * 
     * Example of array configuration item that is converted into
     * ConstructorInjector instance:
     *
     * <code>
     * $item = array(
     *     'type' => 'constructor',
     *     'args' => $args
     * )
     * </code>
     * 
     * Example of array configuration item that is converted into
     * ProviderInjector instance:
     *
     * <code>
     * $item = array(
     *     'type' => 'provider',
     *     'providerClass' => 'Fully\Qualified\Class\Name',
     *     'providerName' => 'ProviderInstanceName',
     *     'providerLifecycle' => 'Singleton'
     * );
     * </code>
     *
     * The 'providerLifecycle' index must contain either 'Singleton'
     * or 'Transient', case sensitive.
     *
     * Example of array configuration item that is converted into
     * CallbackInjector instance:
     *
     * <code>
     * $item = array(
     *     'type' => 'callback',
     *     'func' => $anonymousFunction,
     *     'args' => $args
     * );
     * </code>
     * 
     * @param array $config Configuration array.
     *
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    /**
     * Add an injector.
     * 
     * This method explicitly set the injector instance used.
     * Injectors set this way has the highest priority. Injector
     * classes are saved per their reference instance ID. You can
     * remove explicitly set injector using {@see removeInjector()}.
     * 
     * @param InjectorInterface $injector The injector to be added.
     *
     */
    public function addInjector(InjectorInterface $injector)
    {
        $id = $injector->getReference()->getID();
        $this->injectors[$id] = $injector;
    }
    
    /**
     * Remove injectors explicitly set to the given Reference
     * instance.
     * 
     * @param Reference $reference The reference of the injector to be removed.
     *
     */
    public function removeInjector(Reference $reference)
    {
        $id = $reference->getID();
        unset($this->injectors[$id]);
    }
    
    /**
     * Get the injector that instantiates the object referred to by
     * the given Reference instance.
     *
     * Reads the configuration array and generates an
     * InjectorInterface instance out of it.
     *
     * @throws RuntimeException If the configuration item is not
     *         found, or if the configuration item array is invalid.
     * @param Reference $reference Refers to the instance whose
     *        injector is to be returned.
     * @return InjectorInterface The injector for the given Reference
     *         instance.
     *
     */
    public function getInjector(Reference $reference)
    {
        $id = $reference->getID();
        
        if (!isset($this->config[$id]))
        {
            throw new RuntimeException("ArrayMapConfig error in getting the injector for '{$id}'. No configuration item was found for it.");
        }
        
        $item = $this->config[$id];
        
        if (!isset($item['type']))
        {
            throw new RuntimeException("ArrayMapConfig error in getting the injector for '{$id}'. The configuration item array does not contain the required 'type' index.");
        }
        
        switch ($item['type'])
        {
            case 'constructor':
                return $this->generateConstructorInjector($reference, $item);
            break;
            case 'provider':
                return $this->generateProviderInjector($reference, $item);
            break;
            case 'callback':
                return $this->generateCallbackInjector($reference, $item);
            break;
        }
        
        throw new RuntimeException("ArrayMapConfig error in getting the injector for '{$id}'. The configuration item type '{$item['type']}' is not recognized.");
    }
    
    /**
     * Generate ConstructorInjector instance from the given
     * configuration item array.
     *
     * @throws RuntimeException If the provided configuration item is
     *         invalid.
     * @param Reference $reference Refers to the instance whose
     *        injector is to be generated.
     * @param array $item Configuration item array.
     *
     */
    protected function generateConstructorInjector(Reference $reference, array $item)
    {
        if ($this->isConstructorConfigItemArrayValid($item) == FALSE)
        {
            $id = $reference->getID();
            throw new RuntimeException("ArrayMapConfig error in getting ConstructorInjector for '{$id}'. The configuration item array is not valid.");
        }
        
        return new ConstructorInjector(
            $reference,
            $item['args']
        );
    }
    
    /**
     * Check if the configuration item array typed 'constructor' is
     * a valid item array.
     *
     * @param array $item The configuration item to be checked.
     * @return bool TRUE if valid, FALSE otherwise.
     *
     */
    protected function isConstructorConfigItemArrayValid(array $item)
    {
        return (
            !isset($item['args']) OR
            !is_array($item['args'])
        );
    }
    
    /**
     * Generate ProviderInjector instance from the given
     * configuration item array.
     *
     * @throws RuntimeException If the provided configuration item is
     *         invalid.
     * @param Reference $reference Refers to the instance whose
     *        injector is to be generated.
     * @param array $item Configuration item array.
     *
     */
    protected function generateProviderInjector(Reference $reference, array $item)
    {   
        if ($this->isProviderConfigItemArrayValid($item) == FALSE)
        {
            $id = $reference->getID();
            throw new RuntimeException("ArrayMapConfig error in getting ProviderInjector for '{$id}'. The configuration item array is not valid.");
        }
        
        if ($item['providerLifecycle'] == 'Singleton')
        {
            $lifecycle = 'Singleton';
        }
        else
        {
            $lifecycle = 'Transient';
        }
        
        $providerReference = new Reference(
            (string) $item['providerClass'],
            $lifecycle,
            (string) $item['providerName']
        );
        
        return new ProviderInjector(
            $reference,
            $providerReference
        );
    }
    
    /**
     * Check if the configuration item array typed 'provider' is a
     * valid item array.
     *
     * @param array $item The configuration item to be checked.
     * @return bool TRUE if valid, FALSE otherwise.
     *
     */
    protected function isProviderConfigItemArrayValid(array $item)
    {
        $allowedLifecycle = array('Singleton', 'Transient');
        
        return (
            !isset(
                $item['providerClass'],
                $item['providerName'],
                $item['providerLifecycle']
            ) OR 
            !in_array($item['providerLifecycle'], $allowedLifecycle)
        );
    }
    
    /**
     * Generate CallbackInjector instance from the given
     * configuration item array.
     *
     * @throws RuntimeException If the provided configuration item is
     *         invalid.
     * @param Reference $reference Refers to the instance whose
     *        injector is to be generated.
     * @param array $item Configuration item array.
     *
     */
    protected function generateCallbackInjector(Reference $reference, array $item)
    {
        if ($this->isCallbackConfigItemArrayValid($item) == FALSE)
        {
            $id = $reference->getID();
            throw new RuntimeException("ArrayMapConfig error in getting CallbackInjector for '{$id}'. The configuration item array is not valid.");
        }
        
        return new CallbackInjector(
            $reference,
            $item['func'],
            $item['args']
        );
    }
    
    /**
     * Check if the configuration item array typed 'provider' is a
     * valid item array.
     *
     * @param array $item The configuration item to be checked.
     * @return bool TRUE if valid, FALSE otherwise.
     *
     */
    protected function isCallbackConfigItemArrayValid(array $item)
    {
        return (
            !isset($item['func'], $item['args']) OR
            !is_array($item['args'])
        );
    }
}