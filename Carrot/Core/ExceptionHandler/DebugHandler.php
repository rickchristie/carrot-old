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
 * Debug exception handler.
 *
 * This is Carrot's default exception handler. It gathers trace
 * and useful debugging information, displays it and emulates
 * PHP default error logging operation.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core\ExceptionHandler;

use Exception,
    SplFileObject,
    Carrot\Core\Logbook\LogbookInterface;

class DebugHandler implements HandlerInterface
{   
    /**
     * @var LogbookInterface Carrot core classes' log container,
     *      contains information useful for debugging.
     */
    protected $logbook;
    
    /**
     * @var string Server protocol to be used when setting the
     *      response header.
     */
    protected $serverProtocol;
    
    /**
     * @var string Path to template file to display.
     */
    protected $templateFilePath;
    
    /**
     * Constructor.
     *
     * @param LogbookInterface $logbook Carrot core class's log
     *        container, contains information useful for debugging.
     * @param string $serverProtocol The server protocol to be used
     *        when setting the response header. Optional. Defaults to
     *        'HTTP/1.0'.
     *
     */
    public function __construct(LogbookInterface $logbook, $serverProtocol = 'HTTP/1.0')
    {
        $this->logbook = $logbook;
        $this->serverProtocol = $serverProtocol;
        $this->setTemplateFilePath(
            __DIR__ . DIRECTORY_SEPARATOR . 'Templates' .
            DIRECTORY_SEPARATOR . 'debug.php'
        );
    }
    
    /**
     * Set path to template file that is displayed.
     *
     * This class has a default template file to display, however,
     * you can replace the template with your own using this method.
     *
     * @param string $templateFilePath Absolute path to the template file.
     *
     */
    public function setTemplateFilePath($templateFilePath)
    {
        if (!file_exists($templateFilePath))
        {
            throw new InvalidArgumentException("Carrot's exception handler error in instantiation. The template file '{$templateFilePath}' does not exist.");
        }
        
        $this->templateFilePath = $templateFilePath;
    }
    
    /**
     * Handles the uncaught exception.
     * 
     * As this is the default Carrot's exception handler, it doesn't
     * do anything other than generating and presenting debugging
     * information in HTML format to make development easier.
     * 
     * @param Exception $exception The uncaught exception.
     *
     */
    public function handle(Exception $exception)
    {
        $this->logException($exception);
        $this->setErrorHeader();
        $outputBuffer = $this->getAndCleanOutputBuffer();
        $pageTitle = $this->generatePageTitle($exception);
        $summaryCode = $this->getSummaryCode($exception->getFile(), $exception->getLine());
        $stackTrace = $this->getFormattedStackTrace($exception);
        require $this->templateFilePath;
    }
    
    /**
     * Emulates PHP's default logging behavior.
     *
     * Will only log the exception if log_errors is activated in PHP.
     * Does not do anything fancier than calling vanilla error_log()
     * function.
     *
     * @see handler()
     * @param Exception $exception The exception instance to be logged.
     *
     */
    protected function logException(Exception $exception)
    {
        if (ini_get('log_errors'))
        {
            error_log($exception->__toString());
        }
    }
    
    /**
     * Sends a 500 internal server error response header, but only
     * does so if header hasn't been sent already.
     *
     * @see initialize()
     *
     */
    protected function setErrorHeader()
    {
        if (headers_sent())
        {
            return;
        }
        
        header("{$this->serverProtocol} 500 Internal Server Error");
    }
    
    /**
     * Gets the summary code information in an array.
     * 
     * This method takes 5 lines before the center line number and 5
     * lines after the center line number and puts them into an array.
     * The resulting array example:
     *
     * <code>
     * $summaryCode = array(
     *     0 => array(
     *         'class' => 'odd',
     *         'lineNumber' => '123',
     *         'contents' => 'public function __construct()'
     *     ),
     *     1 => array(
     *         'class' => 'even',
     *         'lineNumber' => '124',
     *         'contents' => '{'
     *     ),
     *     ...
     * );
     * </code>
     *
     * This 'class' attribute will be 'odd' or 'even' according to the
     * index number. For the center line number, it will be appended
     * with 'current'. The first and last line class will be appended
     * with 'first' and 'last' correspondingly.
     * 
     * @param string $filePath Absolute path to the file.
     * @param int $centerLineNumber The center line number.
     * @return array The summary code array.
     *
     */
    protected function getSummaryCode($filePath, $centerLineNumber)
    {
        $file = new SplFileObject($filePath);
        $startingLine = ($centerLineNumber - 6 <= 0) ? 0 : $centerLineNumber - 6;
        $file->seek($startingLine);
        $summaryCode = array();
        
        while (!$file->eof())
        {
            $lineDisplayed = count($summaryCode);
               
            if ($lineDisplayed >= 11)
			{
				break;
			}
			
			$class = ($lineDisplayed % 2 == 0) ? 'even' : 'odd';
			$currentLine = $file->key() + 1;
			
			if ($currentLine == $centerLineNumber)
			{
			    $class .= ' current';
			}
			
			$contents = htmlspecialchars($file->current(), ENT_QUOTES);
			$summaryCode[] = array
			(
                'class' => $class,
                'lineNumber' => $currentLine,
                'contents' => $contents
			);
			$file->next();
        }
        
        $summaryCode[0]['class'] .= ' first';
        $summaryCode[count($summaryCode) - 1]['class'] .= ' last';
        return $summaryCode;
    }
    
    /**
     * Returns a formatted array of stack trace information.
     *
     * Constructs and return an array containing file, function,
     * function arguments, and summary code information, all are
     * formatted and ready to be displayed in the template. Example
     * return:
     *
     * <code>
     * $stackTrace = array(
     *     0 => array(
     *         'fileInfo' => '0. /path/to/file.php on line 13',
     *         'functionInfo' => 'App\Class->methodName',
     *         'functionArgs' => '<pre class="grey">No arguments</pre>',
     *         'summaryCode' => array(
     *             ...
     *         )
     *     ),
     *     ...
     * );
     * </code>
     *
     * For the array structure of the summary code,
     * {@see getSummaryCode()}.
     *
     * @param Exception $exception
     * @return array Formatted array of stack trace information.
     *
     */
    protected function getFormattedStackTrace(Exception $exception)
    {
        $formattedStackTrace = array();
        $rawStackTrace = $exception->getTrace();
        
        foreach ($rawStackTrace as $index => $rawTrace)
        {
            $formattedStackTrace[$index]['fileInfo'] = $this->generateStackTraceFileInfo($index, $rawTrace);
            $formattedStackTrace[$index]['functionInfo'] = $this->generateStackTraceFunctionInfo($rawTrace);
            $formattedStackTrace[$index]['functionArgs'] = $this->generateFunctionArgsInfo($rawTrace);
            $formattedStackTrace[$index]['summaryCode'] = array();
            
            if (isset($rawTrace['file'], $rawTrace['line']))
            {
                $formattedStackTrace[$index]['summaryCode'] = $this->getSummaryCode($rawTrace['file'], $rawTrace['line']);
            }
        }
        
        return $formattedStackTrace;
    }
    
    /**
     * Returns formatted exception page title in HTML string.
     *
     * @param Exception $exception
     *
     */
    protected function generatePageTitle(Exception $exception)
    {
        $class = get_class($exception);
        $errorMessage = htmlspecialchars($exception->getMessage(), ENT_QUOTES);
        $errorCode = htmlspecialchars($exception->getCode(), ENT_QUOTES);
        $filePath = htmlspecialchars($exception->getFile(), ENT_QUOTES);
        $lineNumber = htmlspecialchars($exception->getLine(), ENT_QUOTES);
        return "{$errorMessage} <span>{$class} ({$errorCode}) in file {$filePath} on line {$lineNumber}.</span>";
    }
    
    /**
     * Returns the formatted file information string for the given trace.
     *
     * @param int $index The stack trace level.
     * @param array $rawTrace The raw trace array for the given level.
     *
     */
    protected function generateStackTraceFileInfo($index, array $rawTrace)
    {
        if (isset($rawTrace['file'], $rawTrace['line']))
        {
            $filePath = htmlspecialchars($rawTrace['file'], ENT_QUOTES);
            $lineNumber = htmlspecialchars($rawTrace['line'], ENT_QUOTES);
            return "{$index}. {$filePath} on line {$lineNumber}.";
        }
        
        return 'No file used, most likely internal PHP call.';
    }
    
    /**
     * Returns the formatted function information string for the given trace.
     *
     * @param array $rawTrace The raw trace array to be extracted.
     * 
     */
    protected function generateStackTraceFunctionInfo(array $rawTrace)
    {
        if (!isset($rawTrace['function']))
        {
            return 'No function were used.';
        }
        
        $functionInfo = htmlspecialchars($rawTrace['function'], ENT_QUOTES);
        
        if (isset($rawTrace['class'], $rawTrace['type']))
        {
            $classInfo = htmlspecialchars($rawTrace['class'] . $rawTrace['type'], ENT_QUOTES);
            $functionInfo = $classInfo . $functionInfo;
        }
        
        return $functionInfo;
    }
    
    /**
     * Returns formatted function argument variable dump.
     *
     * The string returned is wrapped in <pre> tags.
     *
     * @param array $rawTrace The raw trace array to be extracted.
     *
     */
    protected function generateFunctionArgsInfo(array $rawTrace)
    {
        if (isset($rawTrace['args']) && !empty($rawTrace['args']))
        {
            ob_start();
            
            // The reason there is a try catch before doing a variable dump
            // here is that in rare cases, PHP will throw ErrorException
            // with the error message 'Property access is not allowed yet',
            // observed when the arguments contain a MySQLi_STMT instance that
            // isn't properly instantiated.
            try
            {
                var_dump($rawTrace['args']);
            }
            catch (Exception $e)
            {
                $errorMessage = $e->getMessage();
            }
            
            $varDump = htmlspecialchars(ob_get_clean(), ENT_QUOTES);
            
            if (!empty($varDump))
            {
                return "<pre>{$varDump}</pre>";
            }
            else if (isset($errorMessage))
            {
                return "<pre class=\"grey\">Unable to dump the contents of the arguments, error message is '{$errorMessage}'.</pre>";
            }
            else
            {
                return '<pre class="grey">Unable to dump the contents of the arguments, unknown error occurred.</pre>';
            }
        }
        
        return '<pre class="grey">No arguments</pre>';
    }
    
    /**
     * Get all output buffers and clean them.
     * 
     * @see handle()
     * @return string All the output buffer strings, concatenated.
     *
     */
    protected function getAndCleanOutputBuffer()
    {
        $string = '';
        
        while (ob_get_level())
        {
            $string .= ob_get_clean();
        }
        
        return $string;
    }
}