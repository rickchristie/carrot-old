<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>An Error Occurred!</title>
	<style>
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
		
		.stacktrace
		{
			font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace;
			font-size: 12px;
			margin: 20px 0;
		}
		
		.stacktrace .filename
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
		
		.stacktrace .funcname
		{
			background: #edf0f3;
			padding: 10px;
			border-left: 1px solid #cbd3db;
			border-right: 1px solid #cbd3db;
			border-bottom: 1px dotted #cbd3db;
		}
		
		.stacktrace .args pre
		{
			margin: 0;
			padding: 10px;
			border-top: none;
			border-top-right-radius: 0px;
			-moz-border-radius-topright: 0px;
			border-top-left-radius: 0px;
			-moz-border-radius-topleft: 0px;
			max-height: 300px;
		}
		
		#wrapper
		{
			width: 650px;
			margin-left: 127px;
			margin-right: 80px;
			margin-top: 30px;
			margin-bottom: 120px;
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
			font-size: 13px;
			line-height: 20px;
			margin: 15px 0;
		}
		
		h2
		{
			font-size: 19px;
		}
		
		h3
		{
			font-size: 17px;
		}
		
		h1, h2, h3, h4, h5, h6
		{
			font-weight: normal;
			margin: 20px 0;
		}
		
		h1
		{
			width: 166px;
			height: 111px;
			position: relative;
			overflow: hidden;
		}
		
		h1 span
		{
			display: block;
			position: absolute;
			width: 100%;
			height: 100%;
			background: url(data:image/png;base64,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 );
			background-repeat: no-repeat;
		}
		
	</style>
</head>
<body>
<div id="header">
	<h1>
		<span></span>
		Carrot
	</h1>
</div>
<div id="wrapper">
	<h2>Error encountered! Severity: <?php echo $variables['err_type'] ?></h2>
	<p>
		&lsquo;<?php echo $variables['err_string'] ?>&rsquo; in <code><?php echo $variables['err_file'] ?></code>, on line <code><?php echo $variables['err_line'] ?></code>.
	</p>
	<div class="code-container">
		<ol>
		<?php
		
			// Grab from five lines before, must not be less than zero
			$file = new SplFileObject($variables['err_file']);
			$seek_line = $variables['err_line'] - 5;
			$seek_line = ($seek_line <= 0) ? 0 : $seek_line;
			$file->seek($seek_line);
			$i = 0;
			
			while (!$file->eof())
			{
				if (++$i > 9)
				{
					break;
				}
				
				$class = ($i%2 == 0) ? 'even' : 'odd';
				$class .= " ival-{$i}";
				$curline = $file->key() + 1;
				
				if ($file->key() == $variables['err_line'] - 1)
				{
					$class .= ' current';
				}
				
				$contents = $file->current();
				$contents = htmlspecialchars($contents);
				echo "<li class=\"{$class}\"><span class=\"line-num\">{$curline}</span><pre> {$contents}</pre></li>";
				$file->next();
			}
			
		?>
		</ol>
	</div>
	<h3>Context at error</h3>
	<?php echo '<pre>', $this->getVarDump($variables['err_context']), '</pre>'; ?>
	
	<?php if (!empty($variables['output_buffer'])): ?>
	<h3>Output buffer</h3>
	<?php echo '<pre>', $this->getVarDump($variables['output_buffer']), '</pre>'; ?>
	<?php endif; ?>
	
	<h3>Stack trace</h3>
	<?php
		$stack_trace = debug_backtrace();
		foreach ($stack_trace as $index => $trace):
	?>
	<div class="stacktrace">
		<div class="filename">
			<?php
				echo "#{$index} ";
				
				if (isset($trace['file'], $trace['line']))
				{
					echo htmlspecialchars($trace['file']), ' on line ', htmlspecialchars($trace['line']);
				}
				else
				{
					echo 'No file used, most likely PHP internal call.';
				}
			?>
		</div>
		<div class="funcname">
			<?php
				if (isset($trace['class'], $trace['type'])) echo htmlspecialchars($trace['class'] . $trace['type']);
				echo htmlspecialchars($trace['function']), '()';
			?>
		</div>
		<div class="args">
			<pre><?php if (isset($trace['args'])) echo $this->getVarDump($trace['args']); else echo '<span class="grey">No arguments</span>' ?></pre>
		</div>
	</div>
	<?php endforeach; ?>
</div>
</body>
</html>