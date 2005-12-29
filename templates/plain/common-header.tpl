<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang ?>" lang="<?= $lang ?>">
<head>
	<title>Openmaillist</title>
	<link rel="stylesheet" href="<?= $cfg['design_dir'] ?>/plain.css" type="text/css" title="plain" />
<?php if(isset($list)) { ?>
	<link rel="alternate" href="rss.php?lid=<?= $list->get_unique_value() ?>" title="RSS Feed" type="application/rss+xml" />
<?php } ?>
</head>

<body>
<h1>Openmaillist</h1>