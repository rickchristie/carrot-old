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

//---------------------------------------------------------------

// ADVANCED USER ONLY
// Full rulebook customization
$autopilot->clearDefaultRulebooks();

// User can add their own rulebooks, but
// here be dragons - make sure you know
// what you're doing:
$autopilot->addInstantiatorRulebook($customRulebook, 'customRulebookName');
$autopilot->addSetterRulebook($customRulebok, 'customRulebookName');

// Certain names are reserved, these two calls
// will throw exception.
$autopilot->addInstantiatorRulebook($customRulebook, 'reflection');
$autopilot->addSetterRulebook($customRulebook, 'standard');

//---------------------------------------------------------------

// LOADING CACHE
$autopilot->getCache()->loadSerialized($serializedArray);


/**
 * 
 *
 */

$autopilot->for('Class:Carrot\MySQLi\MySQLi')->def(
    'hostname',
    'localhost'
);

$autopilot->for('Class:Carrot\MySQLi\MySQLi')->defBatch(array(
    'hostname' => 'localhost',
    'database' => 'carrot_test_db',
    'username' => 'carrot',
    'password' => 'awesome'
));


$autopilot->for('Namespace:App(greedy)')->sub(
    'App\Logger\LoggerInterface',
    'App\Logger\FileLogger@App'
);

$autopilot->for('Class:App\Logger\FileLogger@App')->useCtor(
    $filenamePrefix,
    $filenameExtension,
    $loggingLevel,
    new Reference('App\Config')
);

$autopilot->for('Class:Carrot\MySQLi\MySQLi@:Singleton')->useCallback(
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

$autopilot->for('Class:Carrot\MySQLi\MySQLi*@Main:Singleton')->useProvider(
    'App\ServiceFactory@MainApp',
    'methodName',
    array(new Reference('App\Config'))
);

// This works, but it's awfully too broad if you want to use
// more than one instance of MySQLi. The StandardRulebook
// will parse this as: all instance of Carrot\MySQLi\MySQLi
// as opposed to just Carrot\MySQLi\MySQLi@:Singleton.
$autopilot->for('Class:Carrot\MySQLi\MySQLi')->useProvider(
    'App\ServiceFactory@MainApp',
    'methodName',
    array(new Reference('App\Config'))
);

// Throws an exception, to use StandardRulebook the context
// must be a class, not a namespace
$autopilot->for('Namespace:Carrot\MySQLi\MySQLi')->useProvider(
    'App\ServiceFactory@MainApp',
    'methodName',
    array(new Reference('App\Config'))
);

$autopilot->for('Class:App\Logging\LoggableInterface*')->set(
    'methodName',
    array(
         $primitiveVariable,
         new Reference('Carrot\Application\Blah')   
    )
);


/**
 * Class:Carrot\MySQLi\MySQLi*@Main:Singleton
 *
 */