#!/bin/env php
<?php
if(isset($_SERVER['SERVER_SOFTWARE'])) {
	header('HTTP/1.1 303 See Other');
	header('Location: index.php');
	die('This is an executable, not suited for being served by any webserver.');
}

if($argc <= 2) {
	die(<<<TXT
Usage:		| piper.php LISTNAME DIRECTORY_OF_OML

Will try to store the given message in openmaillist.
The message in question has to be provided through STDIN.

TXT
	);
}
////////////////////////////////////////////////////////////////////////////////
$former_directory	= getcwd();
chdir($argv[2]);

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
$db	= ADONewConnection($cfg['Servers']['DB'][0]['TYPE']);
$db->Connect(	$cfg['Servers']['DB'][0]['HOST'],
		$cfg['Servers']['DB'][0]['USER'], $cfg['Servers']['DB'][0]['PASS'],
		$cfg['Servers']['DB'][0]['DB']);
$db->SetFetchMode(ADODB_FETCH_ASSOC);

// include the backend
$factory	= new oml_factory($db, $cfg['tablenames']);
//$oml		= new openmaillist($db, $factory);

////////////////////////////////////////////////////////////////////////////////

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

if($myMsg->write_to_db()) {
	$theList	= $factory->get_list_by_name($argv[1]);
	$theList->register_message($myMsg);

	// write updated message to DB
	if(!$myMsg->write_to_db()) {
		$myMsg->remove_from_db();
		$factory->delete_empty_threads($theList->get_unique_value());
		die("... message could not be saved.\n");
	} else {
		echo("... message stored successfully.\n");
	}
} else {
	die("... message could not be saved. Maybe it has already been stored.\n");
}

chdir($former_directory);

?>