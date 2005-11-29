<?php
ob_start('ob_gzhandler');
// For security reasons error messages should not be displayed.
ini_set('log_errors', '1');
// ini_set('display_errors', '0');
// error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL);

include('./inc/config.inc.php');
@(include('./inc/config.local.inc.php'))
    or die('You have to create an configuration file, first.');
include('./inc/functions.inc.php');

// MAIN
include('./templates/'.$cfg['theme'].'/common-header.tpl');

// table names with prefixes
$cfg['tablenames']
	= array('Attachements'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Attachements',
		'Lists'		=> $cfg['Servers']['DB'][0]['PREFIX'].'Lists',
		'Messages'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Messages',
		'ThreadMessages'=> $cfg['Servers']['DB'][0]['PREFIX'].'ThreadMessages',
		'Threads'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Threads',
		);

// include the backend
include('./inc/lib/openmaillist.php');
$oml 	= new openmaillist();

?>