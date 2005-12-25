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
include('adodb/adodb.inc.php');
include('./inc/functions.inc.php');

// MAIN
include('./templates/'.$cfg['theme'].'/common-header.tpl');

// table names with prefixes
$cfg['tablenames']
	= array('Attachments'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Attachments',
		'Lists'		=> $cfg['Servers']['DB'][0]['PREFIX'].'Lists',
		'Messages'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Messages',
		'Threads'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Threads',
		);

// set anything important to ADOdb
$db	= ADONewConnection($cfg['Servers']['DB'][0]['TYPE']);
$db->Connect(	$cfg['Servers']['DB'][0]['HOST'],
		$cfg['Servers']['DB'][0]['USER'], $cfg['Servers']['DB'][0]['PASS'],
		$cfg['Servers']['DB'][0]['DB']);
$db->SetFetchMode(ADODB_FETCH_ASSOC);

// include the backend
$factory	= new oml_factory($db, $cfg['tablenames']);
$oml		= new openmaillist($db, $factory);

?>