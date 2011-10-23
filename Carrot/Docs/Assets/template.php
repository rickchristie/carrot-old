<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $page->getTitle('Carrot Documentation » Index', 'Carrot Documentation » ') ?></title>
	<link rel="icon" type="image/png" href="<?php echo $this->getStaticAssetURI('favicon.png') ?>" />
	<link rel="stylesheet" media="screen" href="<?php echo $this->getStaticAssetURI('reset.css') ?>" type="text/css" />
	<link rel="stylesheet" media="screen" href="<?php echo $this->getStaticAssetURI('style.css') ?>" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    <div id="wrapper">
        <h1 id="header">
            <img src="<?php echo $this->getStaticAssetURI('logo.png') ?>" alt="Carrot" />
            <span>0.2.8</span>
        </h1>
        <div id="container" class="clearfix">
            <div id="sidebar">
                <ul class="sectionParents">
                    <li><a href="<?php echo $this->getURI() ?>">Table of contents</a></li>
                    <?php echo $page->renderParentSectionsToList($this->router, $this->routeID) ?>
                </ul>
                <ul class="navigation">
                    <?php echo $page->renderNavigationToList($this->router, $this->routeID) ?>
                </ul>
            </div>
            <div id="content">
                <?php echo $page->getContent() ?>
            </div>
        </div>
    </div>
</body>
</html>