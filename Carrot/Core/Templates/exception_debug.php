<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>500 Internal Server Error. Uncaught Exception!</title>
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
			text-decoration: none;
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
		
		h1
		{
		    font-size: 20px;
		    line-height: 1.4;
		}
		
		h1 span
		{
		    font-size: 16px;
		    color: #666;
		}
		
		h2
		{
			font-size: 18px;
			line-height: 150%;
			margin-top: 40px;
		}
		
		h2 a
		{
            padding-left: 2px;
		    font-size: 13px;
		    text-decoration: none;
		}
    </style>
    <script type="text/javascript">
        // Credit to Dustin Diaz
        // http://www.dustindiaz.com/seven-togglers/
        function toggle(obj)
        {
            var el = document.getElementById(obj);
        	
        	if (el.style.display != 'none' && el.style.display != 'block')
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
</head>
<body>
    <div id="wrapper">
        <h1><?php echo $pageTitle ?></h1>
        <div class="code-container">
            <ol>
                <?php foreach ($summaryCode as $line):  ?>
                <li class="<?php echo $line['class'] ?>">
                    <span class="line-num"><?php echo $line['lineNumber'] ?></span><pre> <?php echo $line['contents'] ?></pre>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <h2>Stack Trace</h2>
        <div id="stack-trace-wrapper">
            <?php foreach ($stackTrace as $index => $trace): ?>
            <div class="exception-stack-trace">
                <div class="filename">
                    <?php echo $trace['fileInfo'] ?>
                    <a href="#" onclick="return toggle('stack-trace-code-<?php echo $index ?>')">Show</a>
                </div>
                <div class="funcname">
                    <?php echo $trace['functionInfo'] ?>(<a href="#" onclick="return toggle('stack-trace-args-<?php echo $index ?>')">arguments</a>)
                </div>
                <div class="args" id="stack-trace-args-<?php echo $index ?>">
                    <?php echo $trace['functionArgs'] ?>
                </div>
                <div class="exception-code-container">
                    <ol id="stack-trace-code-<?php echo $index ?>">
                        <?php foreach ($trace['summaryCode'] as $line):  ?>
                        <li class="<?php echo $line['class'] ?>">
                            <span class="line-num"><?php echo $line['lineNumber'] ?></span><pre> <?php echo $line['contents'] ?></pre>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>