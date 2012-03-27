<?php

/**
 * Dependency Injection in Carrot.
 * 
 * A collection of library classes that is used by the framework
 * to instantiate user's class by dependency injection for them.
 * 
 * Class entries in the configuration file will be read as a
 * DIC reference ID:
 * 
 * <pre>
 * $config['class']['exceptionHandler'] = 'App\CustomExceptionHandler@Main'
 * </pre>
 * 
 * Dependency injection is used to instantiate basically all of
 * userland classes:
 * 
 * - Route classes
 * - Route destination classes
 * - Event handler classes
 * - Exception handler classes
 * 
 * Dependency injection is not used to instantiate framework
 * classes, like:
 * 
 * - Config
 * - Application
 * - Router
 *
 * If the DIC configuration object does not find appropriate
 * injectors, it will try to resolve dependencies automatically
 * using reflection.
 * 
 * Automatic DIC in Carrot allows the user to write testable code
 * without having to muck in <code>new</code> statements and
 * writing boilerplate object creation code.
 * 
 * The user can elect to use the DIC configuration generation
 * feature, which improves performance by making configuration
 * static.
 * 
 * DIC configuration object in Carrot can be extended if the user
 * wanted to add new injectors. The framework part of Carrot can
 * be configured to use another DIC configuration class. This
 * means easier development and usage by the user.
 *
 */

/**
 * References.
 * 
 * Reference IDs refer to a specific dependency injection
 * configuration. A full reference ID consists of full class
 * name, configuration name, and lifecycle setting:
 * 
 * <pre>
 * Carrot\MySQLi\MySQLi@Main.Singleton
 * </pre>
 * 
 * The lifecycle setting can be either Transient or Singleton,
 * is optional and defaults to Singleton, so the above reference
 * name can be simplified as such:
 * 
 * <pre>
 * Carrot\MySQLi\MySQLi@Main
 * </pre>
 * 
 * The configuration name is specific to each class, and is also
 * optional. If you don't have more than one injection
 * configuration for the class, you might want to consider
 * leaving the configuration name out, creating an 'unnamed
 * reference' (a fancy name for reference with empty
 * configuration name string):
 * 
 * <pre>
 * Carrot\MySQLi\MySQLi
 * </pre>
 * 
 * The above reference name essentially is the same as:
 * 
 * <pre>
 * Carrot\MySQLi\MySQLi@.Singleton
 * </pre>
 *
 */

$reference = new Reference('Carrot\Request\Request');
$reference = new Reference('Carrot\MySQLi\MySQLi@Backup');
$reference = new Reference('Carrot\MySQLi\MySQLi@Backup.Transient');
$reference = new Reference('Carrot\MySQLi\PathProcessor.Transient');

/**
 * You can use the DIC to add constants that can later be
 * referenced in dependency injection configuration.
 *
 */

$diConfig->addConstant('hostname', 'localhost');
$diConfig->addConstant('database', 'stockbrokers');
$diConfig->addConstant('username', 'stockbrokers');
$diConfig->addConstant('password', 'password');
$diConfig->getConstant('hostname');

/**
 * Adding the constructor injector.
 * 
 * Parameters after the reference IDs are treated as constructor
 * arguments. Reference objects in the constructor arguments
 * will be resolved recursively.
 *
 */

$diConfig->addConstructorInjector(
    'App\Controller\PostController',
    new Reference('Carrot\MySQLi\PostMapper'),
    new Reference('Carrot\Pagination\Pagination'),
    new Reference('Carrot\SearchParameters\SearchParameters')
);

$diConfig->addConstructorInjector(
    'Carrot\MySQLi\MySQLi@Main',
    $diConfig->getConstant('hostname'),
    $diConfig->getConstant('database'),
    $diConfig->getConstant('username'),
    $diConfig->getConstant('password')
);

/**
 * Callback injectors means using anonymous functions to contain
 * injection logic.
 * 
 * Parameters after the anonymous functions are treated as
 * arguments to the function.
 *
 */

$diConfig->addCallbackInjector(
    'App\Controller\PostController',
    function(MySQLi $mysqli)
    {
        return new PostController($mysqli);
    },
    new Reference('Carrot\MySQLi\MySQLi')
);

/**
 * You can encapsulate the object creation logic inside provider
 * classes, which can then be assigned to a specific reference ID.
 * 
 * Your provider class must implement ProviderInterface. Specify
 * the reference ID to your provider class, which will also be
 * instantiated by the DIC.
 * 
 * Providers are basically factory classes which are instantiated
 * by the DIC.
 *
 */

$diConfig->addProviderInjector(
    'Carrot\MySQLi\MySQLi',
    'App\Provider\MySQLiProvider'
);

/**
 * Users can create their own custom injectors and add them using
 * this method. Alternatively, they should be also be able to
 * extend the configuration class to add helper methods that
 * use their custom injectors.
 *
 */

$diConfig->addInjector(
    'Carrot\MySQLi\MySQLi',
    new CustomInjector
);

/**
 * You can bind a reference ID to another one. For example, you
 * can bind an interface to a concrete implementation. Bindings
 * are only resolved when there are no specific injectors.
 * 
 * You can also use the bind method to bind an already
 * instantiated object to the DIC. If the second parameter is
 * an object, it will be the one returned by the container when
 * the reference ID is needed.
 * 
 * Bindings are only evaluated when Carrot's DIC tries to resolve
 * dependencies automatically.
 *
 */

$diConfig->pointTo(
    'Carrot\Session\SessionStorageInterface',
    'Carrot\Session\NativeSessionStorage'
);

$diConfig->pointTo(
    'Carrot\Session\SessionStorageInterface',
    $nativeSessionStorage
);

/**
 * You can tell the DIC to perform setter injection to specific
 * classes and/or namespaces.
 * 
 * Setter injections will be done immediately after the object
 * has been created. This applies to objects resolved automatically
 * or via a specific injector. Simply specify the method you
 * want to be called, and the arguments for that method, which
 * will also be resolved recursively.
 *
 */

$diConfig->set(
    'App\Controller\Site\*',
    'setLogger',
    $diConfig->getConstant('loggerConfig'),
    new Reference('App\Logger\AppLogger')
);

/**
 * The configuration object can load static configurations in
 * a PHP array map structure, which help speeds things up a bit.
 *
 */

$diConfig->loadArrayMap($filepath);

/**
 * Injection implementation, with PointerList and SetterList.
 *
 */

function get(Reference $reference)
{
    // Start the stack, clears it first.
    $this->stack->start($reference);
    
    while ($this->stack->isNotEmpty())
    {
        $stackItem = $this->stack->peek();
        $reference = $stackItem->getReference();
        $pointerReference = NULL;
        
        // Run object pool.
        if ($this->objectPool->has($reference))
        {
            $instance = $this->objectPool->get($reference);
            $shouldReturn = $this->stack->pop($instance);
            
            if ($shouldReturn)
            {
                return $instance;
            }
            
            continue;
        }
        
        // Run the direct object pointers.
        if ($this->pointerList->canResolveToObject($reference))
        {
            $instance = $this->pointerList->resolveToObject($reference);
            $this->objectPool->pool($instance, $reference);
            $shouldReturn = $this->stack->pop($instance);
            
            if ($shouldReturn)
            {
                return $instance;
            }
            
            continue;
        }
        
        // Run reference pointers.
        if ($this->pointerList->canResolveToReference($reference))
        {
            // From now on we work on the pointed reference
            // instead of the pointer reference.
            $pointerReference = $reference;
            $reference = $this->pointerList->resolveToReference($pointedReference);
        }
        
        // Instantiate using injector if dependencies are fulfilled,
        // otherwise, add 
        
        
    }
}


/**
 * Dependency injection configuration is done through the
 * Carrot\Autopilot library, which can automatically wires
 * dependencies for most of the cases.
 * 
 * Motivation:
 * 
 * Dependency injection containers are nice, but for most part,
 * it requires you to meticulously configure it, either by
 * annotations, XML, or pure method calling. This is
 * understandable if you have complex dependency injections, but
 * most projects in PHP does not need complex dependency
 * injection behavior. PHP's share nothing architecture means
 * we don't have to worry about thread or request scoping of
 * objects - each request builds its own instances, which in turn
 * means that wiring dependencies in PHP is much more simpler
 * than wiring dependencies in other languages.
 * 
//---------------------------------------------------------------
 * Autopilot aims to be an automatic 
 * 
 * With Autopilot,
 * you can define a set of rules and your classes' dependencies
 * will be resolved on the fly. You don't need to manually
 * configure each and every object in your application (although
 * you could if you wanted to).
 * 
 * Usage:
 * 
 * Tell the Autopilot your rules, and then get instances you
 * want via the Container.
 * 
 * 
 * 
 * 
 * 
 * 
 *
 */

/**
 * Tells the autopilot which default values and instances to use
 * when automatically generating constructor injectors.
 * 
 * These values are only used when Autopilot is trying to
 * automatically resolving constructor parameters. If there is
 * a predefined instantiator for the instance in question, then
 * the instantiator will be used instead.
 *
 */

$autopilot->set(
    'App\*',
    'version',
    '0.78'
);

$autopilot->set(
    '*',
    'version',
    '0.78'
);

/**
//---------------------------------------------------------------
 * Tells the 
 *
 */

$autopilot->sub(
    'App\Log\LoggableInterface!',
    'App\Log\LoggerInterface',
    'App\Log\FileLogger@Main'
);

/**
//---------------------------------------------------------------
 * You can tell Autopilot to run setter methods after
 * instantiation with runSetter() method. 
 *
 */

$autopilot->setter(
    
);

/**
//---------------------------------------------------------------
 * You can tell Autopilot to use specific instantiator for
 * specific references
 * 
 * 
 *
 */

$autopilot->useCtor(
    
);

$autopilot->useCallback(
    
);

$autopilot->useProvider(
    
);


/**
 * Users can manually set injectors using the setInjector()
 * method. Injectors set using this method has the highest
 * priority level, other rules will be ignored if an injector
 * is specifically set using this method.
 *
 */

$autopilot->setInstantiator(
    'App\Log\FileLogger@Main',
    $customInjector
);


/**
//---------------------------------------------------------------
 * Cache mechanism.
 * 
 * Automatic dependency resolution, while nice, is not really
 * good performance wise, because it uses reflection heavily.
 * 
 * Autopilot can generate a 
 *
 */

$autopilot->loadCache(array(
    
));