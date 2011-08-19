<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>500 Internal Server Error. Uncaught Exception!</title>
    <style type="text/css">
        <?php require __DIR__ . DIRECTORY_SEPARATOR . 'exception_debug.css' ?>
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
        	
        	return FALSE;
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