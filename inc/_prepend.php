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

// table names with prefixes
$cfg['tablenames']
	= array('Attachments'	=> $cfg['DB']['PREFIX'].'Attachments',
		'Lists'		=> $cfg['DB']['PREFIX'].'Lists',
		'Messages'	=> $cfg['DB']['PREFIX'].'Messages',
		'Threads'	=> $cfg['DB']['PREFIX'].'Threads',
		);

// set anything important to ADOdb
$db	= ADONewConnection($cfg['DB']['TYPE']);
$db->Connect(	$cfg['DB']['HOST'],
		$cfg['DB']['USER'],
		$cfg['DB']['PASS'],
		$cfg['DB']['DB']);
$db->SetFetchMode(ADODB_FETCH_ASSOC);

// include the backend
$superior	= new oml_manager($db, $cfg['tablenames']);
$oml		= new openmaillist($db, $superior);
$oml->upload_dir	= './'.$cfg['upload_dir'];

?>