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
 *
 */

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
 * Autopilot 
 * 
 * Instantiator:
 * -> Cache
 * -> SubstitutionRulebook (Subs)
 * -> StandardRulebook (Ctor, Callback, Provider)
 * -> ReflectionRulebook (Ctor)
 * 
 * Setter:
 * -> Cache
 * -> StandardRulebook (Regular)
 * 
 * Autopilot\Setter\SetterInterface
 * Autopilot\Setter\RegularSetter
 * Autopilot\Setter\Rulebook\SetterRulebookInterface
 * Autopilot\Setter\Rulebook\StandardRulebook
 * 
 * Autopilot\Instantiator\InstantiatorInterface
 * Autopilot\Instantiator\CtorInstantiator
 * Autopilot\Instantiator\CallbackInstantiator
 * Autopilot\Instantiator\ProviderInstantiator
 * Autopilot\Instantiator\SubstitutionInstantiator
 * Autopilot\Instantiator\Rulebook\InstantiatorRulebookInterface
 * Autopilot\Instantiator\Rulebook\StandardRulebook
 * Autopilot\Instantiator\Rulebook\SubstitutionRulebook
 * Autopilot\Instantiator\Rulebook\ReflectionRulebook
 *
 */

// Comes with default rulebooks.
$instantiatorRulebookList = new InstantiatorRulebookList;
$setterRulebookList = new SetterRulebookList;
$autopilot = new Autopilot(
    $instantiatorRulebookList,
    $setterRulebookList
);

// Let's not worry about performance, we can improve that LATER.
// Right now let's worry about API design and capabilities.

//---------------------------------------------------------------

// ReflectionRulebook (instantiator)
// Less magic, but clearer.
$autopilot->getInstantiatorRulebook('reflection')->setDefaultCtorArg(
    'Namespace:App*', // Context string
    'version', // Variable name
    'blah' // Value
);

// Shortcut method for the above:
$autopilot->def(
    'Namespace:App(greedy)', // Context String
    'mysqli', // Variable Name
    new Reference('Carrot\MySQLi\MySQLi') // This works too, due to the nature of CtorInstantiator
);

$autopilot->defBatch(
    'Namespace:App*', // Context String
    $batch // Batch array
);

// If you can def batch like this, why bother
// setting constructor injector via StandardRulebook?
// CtorInstantiator can inject *by reference*
// The below should work if you need only one MySQLi, if you have
// two or more MySQLi instances, you're going to need
// StandardRulebook.
$autopilot->defBatch(
    'Class:MySQLi@Main:Singleton',
    array(
        'hostname' => $hostname,
        'password' => $password,
        'database' => $database,
        'hostname' => $hostname
    );
);

//---------------------------------------------------------------

// SubstitutionRulebook (instantiator)
// Less magic, but clearer
$autopilot->getInstantiatorRulebook('substitution')->substitute(
    'App\LoggerInterface',
    'App\FileLogger'
);

// Shortcut method for the above:
$autopilot->sub(
    'App\LoggerInterface',
    'App\FileLogger'
);

//---------------------------------------------------------------

// StandardRulebook (instantiator)
// Less magic, but clearer
$autopilot->getInstantiatorRulebook('standard')->addCtorInstantiator(
    'App\Controller\PostController',
    array(
        new Reference('Carrot\MySQLi\PostMapper'),
        new Reference('Carrot\Pagination\Pagination'),
        new Reference('Carrot\SearchParameters\SearchParameters')
    )
);

$autopilot->getInstantiatorRulebook('standard')->addCallbackInstantiator(
    'App\Controller\PostController',
    $function, // anonymous function or array callback, used to create Carrot\Type\Callback
    array(
        new Reference('Carrot\MySQLi\MySQLi'),
        new Reference('App\Pagination')
    )
);

$autopilot->getInstantiatorRulebook('standard')->addProviderInstantiator(
    'App\Controller\PostController@Main',
    'App\Controller\PostConrollerProvider',
    'methodName',
    array(
        new Reference('Carrot\MySQLi\MySQLi')
    );
);

// Shortcut method for the above:
$autopilot->useCtor('App\Controller\PostController', array(
    new Reference('Carrot\MySQLi\PostMapper'),
    new Reference('Carrot\Pagination\Pagination'),
    new Reference('Carrot\SearchParameters\SearchParameters')
));

$autopilot->useCallback('Carrot\MySQLi\MySQLi@Main',
    $function,
    $args
);

$autopilot->useProvider('App\Controller\PostController@Main',
    'App\Controller\PostConrollerProvider', // reference string
    'methodName',
    array(
        new Reference('Carrot\MySQLi\MySQLi')
    );
);

//---------------------------------------------------------------

/**
 * People will need to:
 * 
 * - Run specific setter methods on specific contexts
 * - Run specific setter methods on specific Autopilot reference.
 * 
 * What if both overlap?
 * 
 * - ReferenceRulebook
 * - ContextRulebook
 * - ReflectionRulebook
 * 
//---------------------------------------------------------------
 * Each of the rulebook can result in more than one setter (maybe
 * setter list?).
 * 
 * 
 * 
 * Setters will:
 * 
 * - Throw RuntimeException if method does not exist.
 *
 */

// ContextRulebook
// Less magic, but clearer
$autopilot->getSetterRulebook('standard')->add

//---------------------------------------------------------------

// Should setter support multiple setter calls for one
// method on an instance.

// StandardRulebook (setter)
// Less magic, but clearer
$autopilot->getSetterRulebook('standard')->addReferenceSetter(
    'Class:App\LoggableInterface*', // context of the setter
    'methodName',
    array(new Reference('App\Logger\FileLogger'))
)

// Shortcut method for the above:
$autopilot->set(
    'Class:App\LoggableInterface*', // context of the setter
    'methodName',
    array(new Reference('App\Logger\FileLogger'))
);


/**
 * 
 *
 */

$autopilot->on('Class:Carrot\MySQLi\MySQLi')->def(
    'hostname',
    'localhost'
);

$autopilot->on('Class:Carrot\MySQLi\MySQLi')->defBatch(array(
    'hostname' => 'localhost',
    'database' => 'carrot_test_db',
    'username' => 'carrot',
    'password' => 'awesome'
));


$autopilot->on('Namespace:App(greedy)')->sub(
    'App\Logger\LoggerInterface',
    'App\Logger\FileLogger@App'
);

$autopilot->on('Class:App\Logger\FileLogger@App')->useCtor(
    $filenamePrefix,
    $filenameExtension,
    $loggingLevel,
    new Reference('App\Config')
);

$autopilot->on('Class:Carrot\MySQLi\MySQLi@:Singleton')->useCallback(
    function(Config $config)
    {
        return new Carrot\MySQLi\MySQLi(
            $config->getHostname(),
            $config->getUsername(),
            $config->getPassword(),
            $config->getDatabase()
        );
    },
    new Reference('App\Config')
);

$autopilot->on('Class:Carrot\MySQLi\MySQLi*@Main:Singleton')->useProvider(
    'App\ServiceFactory@MainApp',
    'methodName',
    array(new Reference('App\Config'))
);

// This works, but it's awfully too broad if you want to use
// more than one instance of MySQLi. The StandardRulebook
// will parse this as: all instance of Carrot\MySQLi\MySQLi
// as opposed to just Carrot\MySQLi\MySQLi@:Singleton.
$autopilot->on('Class:Carrot\MySQLi\MySQLi')->useProvider(
    'App\ServiceFactory@MainApp',
    'methodName',
    array(new Reference('App\Config'))
);

// Throws an exception, to use StandardRulebook the context
// must be a class, not a namespace
$autopilot->on('Namespace:Carrot\MySQLi\MySQLi')->useProvider(
    'App\ServiceFactory@MainApp',
    'methodName',
    array(new Reference('App\Config'))
);

$autopilot->on('Class:App\Logging\LoggableInterface*')->set(
    'methodName',
    array(
         $primitiveVariable,
         new Reference('Carrot\Application\Blah')   
    )
);

/* 

Example of an Autopilot log output:

#0: App\Controller\PostController@:Singleton
    Getting Instantiator: (toggle)
        Cache: Carrot\Autopilot\RuntimeCache: Nothing found.
        Consult: Carrot\Autopilot\Instantiator\Rulebook\SubstitutionRulebook: No matching rules.
        Consult: Carrot\Autopilot\Instantiator\Rulebook\StandardRulebook: No matching rules.
        Consult: Carrot\Autopilot\Instantiator\Rulebook\ReflectionRulebook: Found!
        Using: Carrot\Autopilot\Instantiator\CtorInstantiator
        Using: CtorArg: 'varName': 'varContents'
        Using: CtorArg: App\DataSource\PostDataSource@:Singleton (link)
    Getting Setter:
        Cache: Carrot\Autopilot\RuntimeCache: Nothing found.
        Consult: Carrot\Autopilot\Setter\Rulebook\ContextRulebook: Found!
        Using: Carrot\Autopilot\Setter\RegularSetter
        Method: setLogger()
        Method: Arg: 'argumentValue'
        Method: Arg: App\Logger\FileLogger@Controller:Singleton (link)
        Method: setVersion()
        Method: Arg: '0.2.8'

#2: App\Controller\PostDataSource@:Singleton
    Getting Instantiator:
        Cache: Carrot\Autopilot\RuntimeCache: Nothing found.
        Consult: Carrot\Autopilot\Instantiator\Rulebook\SubstitutionRulebook: No matching rules.
        Consult: Carrot\Autopilot\Instantiator\Rulebook\StandardRulebook: Found!
        Using: Carrot\Autopilot\Instantiator\ProviderInstantiator
        Using: Provider: App\Factory\DataSourceFactory@:Singleton
        Using: Method: 'getPostDataSource'
        Using: DependencyList: App\DataSource\PostDataSource@:Singleton (link)
        Using: DependencyList: App\View\PostView@:Singleton (link)
    Getting Setter:
        Cache: Carrot\Autopilot\RuntimeCache: Nothing found.
        Consult: Carrot\Autopilot\Setter\Rulebook\ContextRulebook: Nothing found.

Here's what a cache log will look like:

#1: App\Controller\PostController@:Singleton
    Getting Instantiator:
        Cache: Carrot\Autopilot\RuntimeCache: Found!
        Using: Carrot\Autopilot\Instantiator\CtorInstantiator
        Using: CtorArg: 'varName': 'varContents'
        Using: CtorArg: App\DataSource\PostDataSource@:Singleton (link)
    Getting Setter:
        Cache: Carrot\Autopilot\RuntimeCache: Found!
        Using: Carrot\Autopilot\Setter\RegularSetter
        Method: setLogger()
        Method: Arg: 'argumentValue'
        Method: Arg: App\Logger\FileLogger@Controller:Singleton (link)
        Method: setVersion()
        Method: Arg: '0.2.8'

*/

// DRAFT OF CONTAINER LOGIC
// USING STACK OBJECT

public function get(Reference $reference)
{
    $stack = new Stack(
        $this->autopilot,
        $reference
    );
    
    while ($stack->isNotEmpty())
    {
        $item = $stack->peek();
        $reference = $item->getReference();
        
        if ($item->isInstantiated() == FALSE)
        {
            // Get it from object pool
            if ($this->objectPool->has($reference))
            {
                $instance = $this->objectPool->get($reference);
                $item->register->($instance);
                continue;
            }
            
            // Instantiate it if we have fulfilled its dependencies.
            if ($item->hasFulfilledInstantiatorDependencies())
            {
                $instance = $item->instantiate();
                $this->objectPool->set(
                    $reference,
                    $instance
                );
                
                continue;
            }
            
            // If we can't, add its dependencies to the stack.
            $stack->push($item->getInstantiatorDependencyList());
            continue;
        }
        
        if ($item->isSet() == FALSE)
        {
            // Set it if we have fulfilled its dependencies.
            if ($item->hasFulfilledSetterDependencies())
            {
                $item->set();
                continue;
            }
            
            // Otherwise, add its dependencies to the stack.
            $stack->push($item->getSetterDependencyList());
        }
        
        if ($item->isLast())
        {
            return $item->getInstance();
        }
        
        $stack->pop();
    }
}