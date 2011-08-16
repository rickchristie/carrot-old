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
 * Exception Handler
 * 
 * This is Carrot's default exception handler. It doesn't do
 * anything other than gathering debugging information and
 * constructs an appropriate page for displaying them. This is
 * useful when developing, however it is recommended that you
 * create your own exception handler when your code goes to
 * production. In your custom exception handler, you might want
 * to do things like emailing relevant information to the
 * administrator.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Exception;
use InvalidArgumentException;
use SplFileObject;
use Carrot\Core\Interfaces\ExceptionHandlerInterface;

class ExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * @var string Exception template file path.
     */
    protected $templateFilePath;
    
    /**
     * Constructs the exception handler object.
     * 
     * You don't need to specify the template file path if you didn't
     * change the default file structure of Carrot after you
     * downloaded it.
     *
     * You can inject your own template file to this class:
     *
     * <code>
     * $handler = new ExceptionHandler('/path/to/templates/exception.php');
     * </code>
     * 
     * @param string $templateFilePath Absolute path to the template file. Optional.
     *
     */
    public function __construct($templateFilePath = '')
    {
        if (!$templateFilePath)
        {
            $templateFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . 'exception_debug.php';
        }
        
        if (!file_exists($templateFilePath))
        {
            throw new InvalidArgumentException("Carrot's exception handler error in instantiation. The template file path does not exist ({$templateFilePath}).");
        }
        
        $this->templateFilePath = $templateFilePath;
    }
    
    /**
     * Returns a response instance containing debugging information.
     * 
     * As this is the default Carrot's exception handler, it doesn't
     * do anything other than presenting you with debugging
     * information to make your development time easier.
     * 
     * @param Exception $exception The exception to be handled.
     * @return Response Instance of response with debugging information as its body.
     *
     */
    public function handle(Exception $exception)
    {
        $pageTitle = $this->generatePageTitle($exception);
        $summaryCode = $this->getSummaryCode($exception->getFile(), $exception->getLine());
        $stackTrace = $this->getFormattedStackTrace($exception);
        $body = $this->getResponseBody($pageTitle, $summaryCode, $stackTrace);
        return new Response($body, 500);
    }
    
    /**
     * Loads the template file and returns it in string.
     * 
     * Uses output buffering to return it as a string.
     *
     * @param string $pageTitle Formatted page title for display.
     * @param array $summaryCode Array containing summary of the code where the exception originated.
     * @param array $stackTrace Formatted stack trace information.
     *
     */
    protected function getResponseBody($pageTitle, array $summaryCode, array $stackTrace)
    {
        ob_start();
        require $this->templateFilePath;
        return ob_get_clean();
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
            var_dump($rawTrace['args']);
            $varDump = htmlspecialchars(ob_get_clean(), ENT_QUOTES);
            return "<pre>{$varDump}</pre>";
        }
        
        return '<pre class="grey">No arguments</pre>';
    }
}