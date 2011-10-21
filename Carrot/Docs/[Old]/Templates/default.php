<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>
	<style type="text/css">
	body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #333; line-height: 1.5 }
	#wrapper { width: 800px; margin: 100px auto 100px auto; }
	#sidebar { width: 200px; float: left; font-size: 12px; }
	#sidebar ul { margin: 0 0 23px 0; padding: 0; list-style-type: none; line-height: 1.3; }
	#sidebar ul li { margin: 7px 0; }
	#sidebar a { text-decoration: none; color: #7a7a7a; border: none; }
	#sidebar a.current { color: #000; }
	#sidebar a:hover { color: #000; }
	#sidebar h3 { margin: 0 0 12px 0; font-size: 15px; }
	#content { width: 540px; margin-left: 230px }
	#content ul { margin: 20px 0 22px 0; padding: 0 0 0 0; list-style-type: none; }
	#content ol { margin: 20px 0 22px 0; padding: 0 0 0 50px; }
	#content ul li { margin: 8px 0; padding: 0 0 0 40px; background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAPxQTFRF/Pn2+PDo5820tWUXtmca9uziyGYJ/2kE/3UF2E8G/2gG/5UJzJVg+/fz03UI004H01IG6XEI4HoH4ODPz08GuW4ms2ESls6l1mMI797Ow+DKLJVICoMp4loF6VAF+fPu/34FknYquVwI5n4G/40I/v3869bCzZdj2bCISKli1uvbZ7R7FYIy/5EIuWEI71MEwX49+vXx/5oJ/1kF2lUF4e/l028I2+zg6tO8/3YH48WoEIQp0J1rt2og8fjzyFEH/4oH/4MI/4EI+PHrxFsItGMVemgUzujV7drI2HMIE4Ix4vHm/10EuGwj4L+fwHw6/50Iv3o3ypFa////MSaHZAAAAFR0Uk5T//////////////////////////////////////////////////////////////////////////////////////////////////////////////8AU/dy0QAAAI5JREFUeNpczzUWw0AUQ9FvCjOzw8zMzJxo/3tJlbHHKu9R8wiGEW5GqNbCJw4Qi1eWSkeDwTUxyzSlsfYY1Y+udy4UZQB/Nt3o+h4CA1okLWv7pU1/wFnum7f54ZwBlJfnPtmYRAagUqBl88oqA+ApWXsRhw4gfFepfVEHgBh0fwrEhZUPU+Jbd86fAAMAJ31C2sCnrroAAAAASUVORK5CYII=) 18px 0px no-repeat; }
	h1 { font-size: 26px; margin: 20px 0 20px 0; }
	h2 { font-size: 19px; margin: 20px 0 20px 0; }
	h3 { font-size: 15px; margin: 15px 0 15px 0; }
	h1, h2, h3, h4, h5, h6 { line-height: 1.2; font-weight: normal; color: #000; }
	code { font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace; font-size: 90%; }
	pre { font-family: 'Monaco', 'Consolas', 'Courier', 'Courier New', monospace; font-size: 12px; display: block; overflow: auto; background: #edf0f3; border: 1px solid #cbd3db; line-height: 17px; -moz-border-radius: 5px; border-radius: 5px; padding: 15px; margin: 25px 0; }
    p { margin: 15px 0; }
    em { font-style: italic }
    blockquote { margin: 18px 0 18px 40px; font-size: 14px; color: #666; }
    blockquote cite { font-size: 12px; font-style: normal; }
    cite { font-style: normal; }
    a { color: #d56600; text-decoration: none; }
    a:visited { color: #b05400; }
	a:hover { color: #ff843a; border-bottom: 1px solid #dfb97b; }
	a:active { color: #f13900; }
	.clearfix:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }
    .clearfix { display: inline-block; }
    /* Hides from IE-mac \*/ * html .clearfix {height: 1%;} .clearfix {display: block;} /* End hide from IE-mac */
	</style>
</head>
<body>
    <div id="wrapper" class="clearfix">
        <div id="sidebar">
            <?php foreach ($navArray as $topicName => $pages): ?>
                <h3><?php echo $topicName ?></h3>
                <ul>
                    <?php foreach ($pages as $page): ?>
                    <li><a class="<?php echo $page['class'] ?>" href="<?php echo $page['url'] ?>">
                        <?php if ($page['class'] == 'current') echo '@'; ?>
                        <?php echo $page['title'] ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>
        <div id="content">
            <?php echo $pageContent ?>
        </div>
    </div> <!-- #wrapper -->
</body>
</html>