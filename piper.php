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

include('./inc/_prepend.php');

////////////////////////////////////////////////////////////////////////////////

$input = fread(STDIN, 64000);

$myEmail = new oml_email($input);
$myEmail->set_attachment_storage('/tmp');
$myEmail->get_attachments();

try {
	$theList	= $factory->get_list_by_name($argv[1]);
	$oml->put_email($theList, $myEmail);
} catch(Exception $e) {
	die($e->getMessage()."\n");
}

chdir($former_directory);
include('./inc/_append.php');

?>