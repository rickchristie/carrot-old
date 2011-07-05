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
 * Development Exception Page
 * 
 * Carrot's default exception page template. This is to be used on
 * development server only, since it gives out many information to
 * the client. On production, implement ExceptionPageInterface
 * yourself and inject it via the provider to the
 * ExceptionHandler.
 *
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Core;

use Carrot\Core\Interfaces\ExceptionPageInterface;
use SplFileObject;
use Exception;

class DevelopmentExceptionPage implements ExceptionPageInterface
{
    /**
     * @var Exception The exception to be displayed.
     */
    protected $exception;
    
    /**
     * Sets the exception to be displayed.
     *
     * @param Exception $exception
     *
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
    }
    
    /**
     * Prints the exception page template.
     * 
     * The exception page template includes stack trace with code
     * summary, methods/functions called with their arguments and
     * various global variables.
     * 
     */
    public function display()
    {
        $summaryCode = $this->getSummaryCode($this->exception->getFile(), $this->exception->getLine());
        $stacktrace = $this->exception->getTrace();
        $pageTitle = get_class($this->exception) . ' (' . $this->exception->getCode() . ') - ' . htmlspecialchars($this->exception->getMessage(), ENT_QUOTES) . ' in file ' . $this->exception->getFile() . ' on line ' . $this->exception->getLine() . '.';
        echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
        
        ?>
        
        <html>
            <head>
                <title><?php echo $pageTitle ?></title>
                <?php $this->displayCSS() ?>
                <?php $this->displayScript() ?>
            </head>
            <body>
                <div id="wrapper">
                    <h2><?php echo $pageTitle ?></h2>
                    <div class="code-container">
                        <ol>
                            <?php foreach ($summaryCode as $line):  ?>
                            <li class="<?php echo $line['class'] ?>">
                                <span class="line-num"><?php echo $line['lineNumber'] ?></span><pre> <?php echo $line['contents'] ?></pre>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                    <h3>Stack Trace</h3>
                    <div id="stack-trace-wrapper">
                        <?php foreach ($stacktrace as $index => $trace) $this->displayStackTrace($index, $trace); ?>
                    </div>
                    <h3><code>$_SERVER</code></h3>
                    <pre><?php var_dump($_SERVER) ?></pre>
                </div>
            </body>
        </html>
        
        <?php
    }
    
    /**
     * Prints the stack trace information.
     *
     * Information printed out include file name, line number, method
     * called, method arguments and code summary.
     *
     * @param int $index The index number of the trace array.
     * @param array $trace The trace array.
     *
     */
    protected function displayStackTrace($index, array $trace)
    {   
        $stackTraceTitle = $this->getStackTraceTitle($index, $trace);
        $functionName = $this->getFunctionName($trace);
        $functionArguments = $this->getFunctionArguments($trace);
        
        if (isset($trace['file'], $trace['line']))
        {
            $summaryCode = $this->getSummaryCode($trace['file'], $trace['line']);
        }
        else
        {
            $summaryCode = array();
        }
        
        ?>
        <div class="exception-stack-trace">
            <div class="filename"><?php echo $stackTraceTitle ?> <a href="#" onclick="return toggle('stack-trace-code-<?php echo $index ?>')">Show</a></div>
            <div class="funcname"><?php echo $functionName ?>(<a href="#" onclick="return toggle('stack-trace-args-<?php echo $index ?>')">arguments</a>)</div>
            <div class="args" id="stack-trace-args-<?php echo $index ?>"><pre><?php echo $functionArguments ?></pre></div>
            <div class="exception-code-container">
                <ol id="stack-trace-code-<?php echo $index ?>">
                    <?php foreach ($summaryCode as $line):  ?>
                    <li class="<?php echo $line['class'] ?>">
                        <span class="line-num"><?php echo $line['lineNumber'] ?></span><pre> <?php echo $line['contents'] ?></pre>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
        <?php
    }
    
    /**
     * Gets the summary code information in an array.
     * 
     * This method takes 5 lines before the center line number and 5
     * lines after the center line number and puts them into an array.
     * The resulting array example:
     *
     * <code>
     * $summaryCode = array
     * (
     *     0 => array
     *     (
     *         'class' => 'odd',
     *         'lineNumber' => '123',
     *         'contents' => 'public function __construct()'
     *     ),
     *     1 => array
     *     (
     *         'class' => 'even',
     *         'lineNumber' => '124',
     *         'contents' => '{'
     *     )
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
     * Gets the stack trace title.
     * 
     * The stack trace title is the name of the file and the line
     * number. If there is no file or line number, it simply returns
     * the text 'No file used, most likely PHP internal call'.
     * 
     * @param int $index The number of the index.
     * @param array $trace The trace array for the index.
     * @return string Stack trace title.
     *
     */
    protected function getStackTraceTitle($index, array $trace)
    {
        if (isset($trace['file'], $trace['line']))
        {
            return "{$index}. " . htmlspecialchars($trace['file'], ENT_QUOTES) . ' on line ' . htmlspecialchars($trace['line'], ENT_QUOTES);
        }
        else
        {
            return 'No file used, most likely PHP internal call.';
        }
    }
    
    /**
     * Gets the function name and formats it accordingly.
     *
     * @param array $trace The trace array.
     * @return string The formatted function/method name.
     *
     */
    protected function getFunctionName(array $trace)
    {
        $functionName = htmlspecialchars($trace['function'], ENT_QUOTES);
        
        if (isset($trace['class'], $trace['type']))
        {
            $functionName = htmlspecialchars($trace['class'] . $trace['type'], ENT_QUOTES) . $functionName;
        }
        
        return $functionName;
    }
    
    /**
     * Gets the function arguments from the trace, to be printed out.
     *
     * In case there is no arguments, it returns the text 'No
     * arguments', otherwise it gets the var_dump() string of the
     * argument array.
     *
     * @param array $trace The trace array.
     * @return string Function arguments.
     *
     */
    protected function getFunctionArguments(array $trace)
    {
        if (isset($trace['args']))
        {
            return $this->getVarDump($trace['args']);
        }
        else
        {
            return '<span class="grey">No arguments</span>';
        }
    }
    
    /**
     * Returns variable dump in a string, encoding it using htmlentities().
     *
     * @param mixed $var Variable to be dumped.
     * @return string The variable dump, safe for HTML output.
     *
     */
    protected function getVarDump($var)
    {
        ob_start();
        var_dump($var);
        return htmlspecialchars(ob_get_clean(), ENT_QUOTES);
    }
    
    /**
     * Prints the toggle display on/off script.
     *
     * Modified from Dustin Diaz's example at 
     * {@link http://www.dustindiaz.com/seven-togglers/}.
     *
     */
    protected function displayScript()
    {
        ?>
        <script type="text/javascript">
            // Credit to Dustin Diaz
            // http://www.dustindiaz.com/seven-togglers/
            function toggle(obj)
            {
                var el = document.getElementById(obj);
            	
            	if (el.style.display != 'none' && el.style.display != 'block' && obj != 'stack-trace-wrapper')
            	{
            	    el.style.display = 'block';
            	}
            	else if (el.style.display != 'none')
            	{
            		el.style.display = 'none';
            	}
            	else
            	{
            		el.style.display = 'block';
            	}
            	
            	return false;
            }
        </script>
        <?php
    }
    
    /**
     * Prints the <style> tag along with the styles.
     *
     */
    protected function displayCSS()
    {
        ?>
        <style type="text/css">
            body, html
    		{
    			font-family: 'Helvetica', 'Arial', sans-serif;
    			margin: 0;
    			padding: 0;
    			font-size: 13px;
    		}
    		
    		.grey
    		{
    			color: #666;
    		}
    		
    		code
    		{
    			font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace;
    			font-size: 90%;
    		}
    		
    		pre
    		{
    			font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace;
    			font-size: 12px;
    			display: block;
    			overflow: auto;
    			background: #edf0f3;
    			border: 1px solid #cbd3db;
    			line-height: 17px;
    			-moz-border-radius: 5px;
    			border-radius: 5px;
    			padding: 15px;
    		}
    		
    		.code-container
    		{
    			font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace;
    			font-size: 12px;
    			display: block;
    			overflow: auto;
    			background: #edf0f3;
    			border: 1px solid #cbd3db;
    			line-height: 15px;
    			-moz-border-radius: 5px;
    			border-radius: 5px;
    			overflow: hidden;
    			padding: 0;
    			margin: 15px 0;
    		}
    		
    		.code-container ol > li:last-child span
    		{
    			border-bottom-left-radius: 5px;
    			-moz-border-radius-bottomleft: 5px;
    		}
    		
    		.code-container ol > li:last-child pre
    		{
    			border-bottom-right-radius: 5px;
    			-moz-border-radius-bottomright: 5px;
    		}
    		
    		.code-container ol > li:first-child span
    		{
    			border-top-left-radius: 5px;
    			-moz-border-radius-topleft: 5px;
    		}
    		
    		.code-container ol > li:first-child pre
    		{
    			border-top-right-radius: 5px;
    			-moz-border-radius-topright: 5px;
    		}
    		
    		.code-container ol
    		{
    			margin: 0;
    			padding: 0;
    			list-style-type: none;
    		}
    		
    		.code-container ol li
    		{
    			margin: 0;
    			padding: 0 0 0 0px;
    		}
    		
    		.code-container ol li span.line-num
    		{
    			display: block;
    			float: left;
    			margin: 0;
    			padding: 4px 6px;
    			background: #e1eeff;
    			border-right: 1px solid #cbd3db;
    			width: 25px;
    			text-align: right;
    			overflow: hidden;
    		}
    		
    		.code-container ol li.current span.line-num
    		{
    			background: #fe6b6b;
    			border-right: 1px solid #fe6b6b;
    			color: #fff;
    		}
    		
    		.code-container ol li pre
    		{
    			margin: 0;
    			padding: 4px 0 4px 0px;
    			width: auto;
    			overflow: hidden;
    			background: transparent;
    			border: none;
    			line-height: 15px;
    			-moz-border-radius: 0px;
    			border-radius: 0px;
    		}
    		
    		.code-container ol li.odd pre
    		{
    			background: #f5f8fb;
    		}
    		
    		.code-container ol li.current pre
    		{
    			background: #fe6b6b;
    			color: #fff;
    		}
    		
    		.exception-code-container
    		{
    			font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace;
    			font-size: 12px;
    			overflow: auto;
    			background: #edf0f3;
    			border-left: 1px solid #cbd3db;
    			border-right: 1px solid #cbd3db;
    			border-bottom: 1px solid #cbd3db;
    			border-bottom-right-radius: 5px;
    			-moz-border-radius-bottomright: 5px;
    			border-bottom-left-radius: 5px;
    			-moz-border-radius-bottomleft: 5px;
    			line-height: 15px;
    			overflow: hidden;
    			padding: 0 0 0 0;
    			margin: 0;
    		}
    		
    		.exception-code-container ol
    		{
                display: none;
                border-top: 1px solid #cbd3db;
    			margin: 0;
    			padding: 0;
    			list-style-type: none;
    		}
    		
    		.exception-code-container ol li
    		{
    			margin: 0;
    			padding: 0 0 0 0px;
    		}
    		
    		.exception-code-container ol li span.line-num
    		{
    			display: block;
    			float: left;
    			margin: 0;
    			padding: 4px 6px;
    			background: #e1eeff;
    			border-right: 1px solid #cbd3db;
    			width: 25px;
    			text-align: right;
    			overflow: hidden;
    		}
    		
    		.exception-code-container ol li.current span.line-num
    		{
    			background: #21ad48;
    			border-right: 1px solid #21ad48;
    			color: #fff;
    		}
    		
    		.exception-code-container ol li.last span
    		{
    		    border-bottom-left-radius: 5px;
    			-moz-border-radius-bottomleft: 5px;
    		}
    		
    		.exception-code-container ol li pre
    		{
    			margin: 0;
    			padding: 4px 0 4px 0px;
    			width: auto;
    			overflow: hidden;
    			background: transparent;
    			border: none;
    			line-height: 15px;
    			-moz-border-radius: 0px;
    			border-radius: 0px;
    		}
    		
    		.exception-code-container ol li.odd pre
    		{
    			background: #f5f8fb;
    		}
    		
    		.exception-code-container ol li.last pre
    		{
    		    border-bottom-right-radius: 5px;
    			-moz-border-radius-bottomright: 5px;
    		}
    		
    		.exception-code-container ol li.current pre
    		{
    			background: #21ad48;
    			color: #fff;
    		}
    		
    		.exception-stack-trace
    		{
    			font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace;
    			font-size: 12px;
    			margin: 20px 0 30px 0;
    		}
    		
    		.exception-stack-trace .filename
    		{
    			background: #f5f9fb;
    			border-left: 1px solid #cbd3db;
    			border-right: 1px solid #cbd3db;
    			border-top: 1px solid #cbd3db;
    			border-bottom: 1px solid #cbd3db;
    			padding: 10px;
    			border-top-right-radius: 5px;
    			-moz-border-radius-topright: 5px;
    			border-top-left-radius: 5px;
    			-moz-border-radius-topleft: 5px;
    		}
    		
    		.exception-stack-trace .funcname
    		{
    			background: #edf0f3;
    			padding: 10px;
    			border-left: 1px solid #cbd3db;
    			border-right: 1px solid #cbd3db;
    		}
    		
    		.exception-stack-trace .args
    		{
    		    display: none;
    		}
    		
    		.exception-stack-trace .args pre
    		{
    			margin: 0;
    			padding: 10px;
    			border-top: 1px dotted #cbd3db;
    			border-bottom: 0;
    			border-top-right-radius: 0px;
    			-moz-border-radius-topright: 0px;
    			border-top-left-radius: 0px;
    			-moz-border-radius-topleft: 0px;
    			border-bottom-right-radius: 0px;
    			-moz-border-radius-bottomright: 0px;
    			border-bottom-left-radius: 0px;
    			-moz-border-radius-bottomleft: 0px;
    			max-height: 300px;
    		}
    		
    		#wrapper
    		{
                width: 80%;
    			margin: 90px auto 120px auto;
    		}
    		
    		#header
    		{
    			width: 650px;
    			margin-left: 80px;
    			margin-top: 80px;
    		}
    		
    		a:link
    		{
    			color: #f13900;
    		}
    		
    		a:visited
    		{
    			color: #ff5825;
    		}
    		
    		a:hover
    		{
    			color: #ff843a;
    		}
    		
    		a:active
    		{
    			color: #f13900;
    		}
    		
    		p
    		{
    			font-size: 14px;
    			line-height: 20px;
    			margin: 15px 0;
    		}
    		
    		h1, h2, h3, h4, h5, h6
    		{
    			font-weight: normal;
    			margin: 20px 0;
    		}
    		
    		h2
    		{
    			font-size: 18px;
    			line-height: 150%;
    		}
    		
    		h3
    		{
    		    margin-top: 40px;
    			font-size: 17px;
    		}
    		
    		h3 a
    		{
                padding-left: 2px;
    		    font-size: 13px;
    		    text-decoration: none;
    		}
        </style>	       
        <?php
    }
}