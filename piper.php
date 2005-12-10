#!/bin/env php
<?php
die('This is an executable, not suited for being served by any webserver.');

////////////////////////////////////////////////////////////////////////////////
/* copied from inc/_prepend.php */
include('./inc/config.inc.php');
@(include('./inc/config.local.inc.php'))
	or die('You have to create an configuration file, first.');
include('adodb/adodb.inc.php');
include('./inc/functions.inc.php');

// table names with prefixes
$cfg['tablenames']
	= array('Attachments'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Attachments',
		'Lists'		=> $cfg['Servers']['DB'][0]['PREFIX'].'Lists',
		'Messages'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Messages',
		'Threads'	=> $cfg['Servers']['DB'][0]['PREFIX'].'Threads',
		);

// set anything important to ADOdb
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$db	= ADONewConnection($cfg['Servers']['DB'][0]['TYPE']);
$db->Connect(	$cfg['Servers']['DB'][0]['HOST'],
		$cfg['Servers']['DB'][0]['USER'], $cfg['Servers']['DB'][0]['PASS'],
		$cfg['Servers']['DB'][0]['DB']);

// include the backend
$factory	= new oml_factory($db, $cfg['tablenames']);
$oml		= new openmaillist($db, $factory);

////////////////////////////////////////////////////////////////////////////////
if($argc <= 1) {
	die(<<<TXT
Usage:		| piper.php LISTNAME

Will try to store the given message in openmaillist.
The message in question has to be provided through STDIN.

TXT
	);
} else {
	$input = fread(STDIN, 64000);

	$myEmail = new oml_email($input);
	$myEmail->set_attachment_storage('/tmp');
	$myEmail->get_attachments();

	echo("On processing that message...\n");

	$myMsg		= $factory->get_message();
	$myMsg->let(	$myEmail->get_header('message-id'), $myEmail->get_header('date-send'), $myEmail->get_header('date-received'),
			$myEmail->get_header('from'), $myEmail->get_header('subject'),
			$myEmail->has_attachments() ? 1 : 0,
			$myEmail->get_first_displayable_part(true));

	$theList	= $factory->get_list_by_name($argv[1]);
	$theList->register_message($myMsg);

	// write it to db
	if(!$myMsg->write_to_db()) {
		die("... message could not be saved.\n");
	} else {
		echo("... message stored successfully.\n");
	}
}
?>