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
 * Exception Handler Manager
 *
 * Manages your exception handlers. This class is technically the
 * main exception handler, but it doesn't handle exceptions
 * directly as it only delegates the exception to your registered
 * exception handler.
 *
 * You can bind a specific exception handler instance to be used
 * based on the class of the exception. If, for example, you have
 * the following exception classes:
 *
 * <code>
 * namespace Blog;
 * use Exception;
 * class BlogException extends Exception { }
 * class PostNotFoundException extends BlogException { }
 * </code>
 *
 * You can create a custom exception handler to be used only if
 * PostNotFoundException occurs. Bind the object reference of your
 * exception handler in this class's constructor argument:
 *
 * <code>
 * $manager = new ExceptionHandlerManager(array(
 *     'Blog\PostNotFoundException' => new ObjectReference(
 *         'Blog\PostNotFoundExceptionHandler{Main:Transient}'
 *     )
 * ));
 * </code>
 *
 * Use fully qualified namespace (case sensitive) for binding,
 * without backslash prefix. Your exception handler class must
 * implement ExceptionHandlerInterface. Since the exception
 * handler is instantiated via the DIC, you can tell the DIC to
 * wire the dependencies of your exception handler class.
 * 
 * To bind a default exception handler class, simply bind the
 * object reference to the base Exception class:
 * 
 * <code>
 * $manager = new ExceptionHandlerManager(array(
 *     'Exception' => new ObjectReference(
 *         'Carrot\Core\ExceptionHandler{Main:Transient}'
 *     )
 * ));
 * </code>
 * 
 * If there is more than one match in the handler bindings, the
 * exception class gets the first priority, followed by its
 * parent, grandparent, and so on and so forth.
 *
 * For more information in writing your custom exception handler
 * class, see the docs at
 * {@see Carrot\Core\Interfaces\ExceptionHandlerInterface}.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use ReflectionClass;
use Carrot\Core\Response;
use Carrot\Core\Interfaces\ExceptionHandlerInterface;
use Carrot\Core\Interfaces\ExceptionLoggerInterface;

class ExceptionHandlerManager
{
    /**
     * @var array List of object references and the exception class they were bound to.
     */
    protected $handlerBindings;
    
    /**
     * @var ExceptionLoggerInterface Used to log the uncaught exception.
     */
    protected $logger;
    
    /**
     * @var string Default server protocol for the returned response object. Defaults to 'HTTP/1.0'.
     */
    protected $defaultServerProtocol;
    
    /**
     * @var DependencyInjectionContainer Used to get the instance of the exception handler.
     */
    protected $dic;
    
    /**
     * @var bool True if the exception handler is set, false otherwise.
     */
    protected $set;
    
    /**
     * Constructs the exception handler manager.
     * 
     * When constructing, inject the exception handler bindings array
     * and an implementation of ExceptionLoggerInterface (optional,
     * if not given a default implementation will be used instead).
     *
     * <code>
     * $manager = new ExceptionHandlerManager(array(
     *     'Blog\PostNotFoundException' => new ObjectReference(
     *         'Blog\PostNotFoundExceptionHandler{Main:Transient}'
     *     ),
     *     'Exception' => new ObjectReference(
     *         'Carrot\Core\ExceptionHandler{Main:Transient}'
     *     )
     * ));
     * </code>
     * 
     * @param array $handlerBindings The handler bindings in array.
     * @param ExceptionLoggerInterface The logging interface implementation.
     *
     */
    public function __construct(array $handlerBindings, ExceptionLoggerInterface $logger = null)
    {
        $this->handlerBindings = array();
        $this->set = false;
        
        if (!$logger)
        {
            $logger = new ExceptionLogger;
        }
        
        $this->setHandlerBindings($handlerBindings);
        $this->makeSureThereIsADefaultExceptionHandler();
        $this->defaultServerProtocol = 'HTTP/1.0';
        $this->logger = $logger;
    }
    
    /**
     * Setter injection for the DIC.
     *
     * This class needs the DIC to instantiate the appropriate
     * exception handler class. It is injected via a setter injection
     * so that the user doesn't have to inject the DIC on his/her own.
     *
     * @param DependencyInjectionContainer $dic
     *
     */
    public function setDIC(DependencyInjectionContainer $dic)
    {
        $this->dic = $dic;
    }
    
    /**
     * Setter injection for default server protocol.
     *
     * This class needs a default server protocol string because it
     * needs to set up the response instance returned by the exception
     * handler.
     *
     * @param string $defaultServerProtocol Default server protocol for response instance returned by exception handler.
     *
     */
    public function setDefaultServerProtocol($defaultServerProtocol)
    {
        $this->defaultServerProtocol = $defaultServerProtocol;
    }
    
    /**
     * Main exception handler method.
     * 
     * Logs the error using the the injected (or default)
     * implementation of ExceptionLoggerInterface. Dispatches the
     * exception to the appropriate exception handler and sends the
     * returned response to the client.
     * 
     * @param Exception $exception The exception to be dispatched.
     *
     */
    public function dispatchException(Exception $exception)
    {   
        // A lot of the things in this exception handler is wraped inside
        // a try catch block so that if an exception occurs when we are
        // handling the exception, we still get an error message instead
        // of a debugging nightmare. Also, yes I know this method is long,
        // but I don't want to split methods anymore because if something
        // bad happened while we're handling an exception, debugging it
        // would be a real hassle if everything was spread out in lots
        // of methods.
        
        try
        {
            $this->logger->log($exception);
            $classList = $this->getClassList($exception);
            $exceptionHandlerObjectReference = null;
            
            foreach ($classList as $className)
            {
                if (isset($this->handlerBindings[$className]))
                {
                    $exceptionHandlerObjectReference = $this->handlerBindings[$className];
                    break;
                }
            }
            
            $exceptionHandler = $this->dic->getInstance($exceptionHandlerObjectReference);
            
            if (!($exceptionHandler instanceof ExceptionHandlerInterface))
            {
                $class = get_class($exceptionHandler);
                $this->sendDefaultUncaughtExceptionResponse("
                    <p>
                        An uncaught exception occurs and has been logged,
                        but the class that was supposed to handle it, i.e.
                        <code>{$class}</code>, does not implement the
                        <code>Carrot\Core\Interfaces\ExceptionHandlerInterface</code>.
                        This makes the <code>ExceptionHandlerManager</code>
                        unable handle the current exception.
                    </p>
                ");
            }
            
            $response = $exceptionHandler->handle($exception);
            
            if (!$response OR !($response instanceof Response))
            {
                $class = get_class($exceptionHandler);
                $this->sendDefaultUncaughtExceptionResponse("
                    <p> 
                        An uncaught exception occurred. It was logged and
                        handled, but the exception handler, i.e.
                        <code>{$class}</code> does not return
                        an instance of <code>Carrot\Core\Response</code>
                        to be sent to the user so this page is displayed
                        instead.
                    </p>
                ");
            }
            
            $response->setDefaultServerProtocol($this->defaultServerProtocol);
            $response->send();
        }
        catch (Exception $exceptionWithinException)
        {
            $class = get_class($exceptionWithinException);
            $line = $exceptionWithinException->getLine();
            $message = htmlspecialchars($exceptionWithinException->getMessage(), ENT_QUOTES);
            $file = $exceptionWithinException->getFile();
            
            $this->sendDefaultUncaughtExceptionResponse("
                <p>
                    An uncaught exception occurred and another exception
                    was thrown when trying to handle the exception. This could
                    mean that the error was not properly logged.
                </p>
                <p class=\"message\">
                    <code>{$class}</code> thrown when handling
                    exception. Message: '{$message}' on line {$line} in file
                    <code>{$file}</code>
                </p>
                <p>
                    Common reasons why this happens:
                </p>
                <ul>
                    <li>
                        Exception thrown by the current
                        <code>ExceptionLoggerInterface</code>
                        implementation.
                    </li>
                    <li>
                        Exception thrown by the current
                        <code>ExceptionHandlerInterface</code>
                        implementation.
                    </li>
                    <li>
                        The DIC fails to instantiate the needed
                        <code>ExceptionHandlerInterface</code>
                        implementation.
                    </li>
                </ul>
            ");
        }
    }
    
    /**
     * Sets the exception handler.
     *
     * Checks first so that we don't accidentally set the exception
     * handler twice.
     *
     */
    public function set()
    {
        if (!$this->set)
        {
            set_exception_handler(array($this, 'dispatchException'));
            $this->set = true;
        }
    }
    
    /**
     * Restores the exception handler.
     *
     * Checks first so that we don't accidentally restore the
     * exception handler even though we haven't set it.
     *
     */
    public function restore()
    {
        if ($this->set)
        {
            restore_exception_handler();
            $this->set = false;
        }
    }
    
    /**
     * Gets the class list, including all the parents of the exception.
     *
     * The list is returned in an array format, starting from the
     * exception's class name, to its immediate parent, then to its
     * immediate parent, all the way to the Exception base class.
     * Example return:
     *
     * <code>
     * $array = array(
     *     'Blog\PostNotFoundException',
     *     'Blog\BlogException',
     *     'Exception'
     * );
     * </code>
     *
     * @param Exception $exception The exception instance to be extracted.
     * @return array Array containing the class name and all the parents' class names.
     *
     */
    protected function getClassList(Exception $exception)
    {
        $classList = array();
        $reflection = new ReflectionClass($exception);
        $classList[] = $reflection->getName();
        
        while ($parent = $reflection->getParentClass())
        {
            $classList[] = $parent->getName();
            $reflection = $parent;
        }
        
        return $classList;
    }
    
    /**
     * Makes sure that there is a default exception handler.
     *
     * This class delegates uncaught exceptions to exception handlers
     * based on the exception class. This method makes sure that there
     * is an exception handler bound to the base Exception class,
     * which makes it the default exception handler used. Throws
     * RuntimeException if default exception handler is not set.
     *
     * @throws RuntimeException
     *
     */
    protected function makeSureThereIsADefaultExceptionHandler()
    {
        if (!isset($this->handlerBindings['Exception']) OR 
            !($this->handlerBindings['Exception'] instanceof ObjectReference))
        {
            throw new RuntimeException("ExceptionHandlerManager error in instantiating. Could not find a default exception handler bound to the base Exception class.");
        }
    }
    
    /**
     * Sets the handler bindings.
     *
     * Sets the handler bindings array. Throws
     * InvalidArgumentException if the handler bindings array does not
     * contain the required object reference.
     * 
     * @throws InvalidArgumentException
     * @param array $handlerBindings The array that contains the handler bindings.
     *
     */
    protected function setHandlerBindings(array $handlerBindings)
    {
        foreach ($handlerBindings as $className => $objectReference)
        {
            if (!($objectReference instanceof ObjectReference))
            {
                throw new InvalidArgumentException("ExceptionHandlerManager error in instantiation. Binding for '{$className}' does not contain an instance of Carrot\Core\ObjectReference");
            }
            
            $this->handlerBindings[$className] = $objectReference;
        }
    }
    
    /**
     * Sends a default exception handler response and exits immediately.
     * 
     * In case of unexpected things in handling exception, like the
     * user's exception handler doesn't return an instance of response
     * or the user's exception handler class doesn't implement
     * required interface, or if exceptions occurs in the exception
     * handler method, this method will be used to return a response
     * to the user.
     *
     * If display_errors is turned off, this method will always return
     * a generic 500 internal server error message. If display_errors
     * is turned on the information passed by the caller will be
     * displayed.
     * 
     * @param string $body The body containing some explanation message, in HTML.
     *
     */
    protected function sendDefaultUncaughtExceptionResponse($body)
    {
        if (!ini_get('display_errors'))
        {
            $body = '
                <p>
                    We are sorry for the inconvenience, but an internal
                    server error has occurred. Please contact the administrator
                    to alert him of this error, as this error was not properly
                    handled by the system and error logging efforts might have
                    failed.
                </p>
            ';
        }
        
        $response = new Response("
            <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">
            <html>
            <head>
            	<title>500 Internal Server Error</title>
            	<style type=\"text/css\">
            	   body { width: 500px; margin: 150px; font-family: 'Arial', 'Helvetica', sans-serif; font-size: 13px; line-height: 1.5; }
            	   h1 { font-weight: normal; font-size: 25px }
            	   code { font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace; font-size: 90%; }
            	   p { margin: 15px 0; }
            	   p.message { background: #EDF0F3; border: 1px solid #cbd3db; -moz-border-radius: 5px; border-radius: 5px; padding: 15px;}
            	   ul { margin: 15px 0; padding: 0 0 0 0; list-style-type: none; }
            	   ul li { margin: 8px 0; padding: 0 0 0 40px; background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAaRJREFUeNqc0l8oQ1EcB/DvuXfW7v4gWY3JrNGWPKiJlELhDSnlAU8elPLiyYNXj548+fO49qgm7/IkL1LTZCzNljQMubYx9x6/W1eNdGd+dfqdfuf26Xd+5zLzqh9amCxV+C1el6IwCkHPzfhnfAH2j0JxGxyLtA9WApj0HLOo5lCxoGwxizABhonwcaKG6s/6Mu6gVrF7q7hpn3Heai4ogyvxm5hk5kk6SkZmrMNlgSdRvhIhBGxcwkL6DqPVucaRPqDbp2hdRAgJlpsBZNvb+VQm09Mvy+iQFCoADU0iOj2qpCMuQ4CGiKHHh2ies9vUJRXShDyp8LgFBFzcrSPST4Cuzb8V6KNeSnvt7x91bfV05qI524HTayBxzzbHQ7l5Q0BHtP9ix5dXgh02le4iAg6GoxTD7SvGCdk1BHREa3fDIyuzLSYVtU4BGUnAYYZlqd5GSNYQKIHmKK0FZKXGb+U4s4qIF1iYgOk/ATqivcC6K6dOermKC5uIe7ABQg7+BJRAYxrke1GbEw7hhPZdFQEls1mitUxrvmKgBHJSyn8KMAD+06jQNTPITwAAAABJRU5ErkJggg==); background-repeat: no-repeat; background-position: 18px 1px; }
            	</style>
            </head>
            <body>
                <h1>500 Internal Server Error</h1>
            	{$body}
            </body>
            </html>
        ");
        
        $response->setStatus(500);
        $response->setDefaultServerProtocol($this->defaultServerProtocol);
        $response->send();
        exit;
    }
}