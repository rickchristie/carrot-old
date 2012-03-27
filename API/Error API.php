<?php

/**
 * Error Handling and Debugging in Carrot.
 * 
 * All errors are converted into exceptions in Carrot. This is
 * by design and must not be changed if the framework is to work
 * correctly.
 * 
 * Carrot is a strict development framework and will
 * automatically set error reporting into -1 (show all errors).
 * 
 * Carrot will set a default exception handler, which can be
 * replaced to another class by the user. The exception handler
 * class must implement ExceptionHandlerInterface.
 * 
 * Carrot comes with one development exception handler that
 * honors the PHP <code>display_errors</code> configuration.
 * If it's set to on, it will display exception information,
 * if it's not it will display a generic 500 Internal Server
 * Error message.
 * 
 * Users are suggested to craft their own exception handler
 * classes when deploying their sites. Exception handlers are
 * instantiated by the DIC, so users can inject required objects
 * easily.
 * 
 * Carrot has a debug mode in which it will render a debugging
 * response page if there is an uncaught exception on the user's
 * code. This is not part of the default exception handler and
 * must be activated using the configuration object.
 * 
 * In later versions:
 * 
 * - Exception handlers will receive RoutingLog, ContainerLog,
 *   EventLog, and AutoloaderLog, which can be displayed or not,
 *   depending on the user.
 *
 */

