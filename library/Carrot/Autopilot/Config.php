<?php

namespace Carrot\Injection;

/**
 * Represents the dependency injection configuration for the
 * container. Main responsibility of this class is to generate
//---------------------------------------------------------------
 * injectors for the Container based on the configuration.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class Config
{
    /**
     * Used for listing and resolving aliases.
     * 
     * @var AliasList $aliasList
     */
    private $aliasList;
    
    /**
     * Used for listing and resolving setters.
     * 
     * @var SetterList $setterList
     */
    private $setterList;
    
    /**
     * Constructor.
     * 
     * @param Aliases $aliases
     * @param Setters $setters
     *
     */
    public function __construct(
        AliasList $aliases,
        SetterList $setters
    )
    {
        $this->aliasList = $aliasResolver;
        $this->setterResolver = $setterResolver;
    }
    
    public function point()
    {
        
    }
    
    public function set()
    {
        
    }
    
    public function getAliasList()
    {
        
    }
    
    public function getSetterList()
    {
        
    }
}