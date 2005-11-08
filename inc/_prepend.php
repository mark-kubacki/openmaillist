<?php
ob_start('ob_gzhandler');
// For security reasons error messages should not be displayed.
ini_set('log_errors', '1');
// ini_set('display_errors', '0');
// error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL);

include('config.inc.php4');
	if(is_readable('config.local.inc.php')) {
	    include('config.local.inc.php');
	}
	else if(is_readable('inc/config.local.inc.php')) {
	    include('inc/config.local.inc.php');
	}
	else {
	    die('You have to create an configuration file, first.');
	}

// MAIN
include('templates/'.$cfg['theme'].'/common-header.tpl');

// table names with prefixes
$cfg['tablenames']
	= array('Attachements'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Attachements',
		'Lists'		=> $cfg['Servers']['DB'][0]['PREFIX'].'Lists',
		'Messages'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Messages',
		'ThreadMessages'=> $cfg['Servers']['DB'][0]['PREFIX'].'ThreadMessages',
		'Threads'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Threads',
		);

// include the backend
include('lib/openmaillist.php');
$oml 	= new openmaillist();

?>