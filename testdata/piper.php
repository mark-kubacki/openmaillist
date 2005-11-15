#!/bin/env php
<?php
include('Mail/mimeDecode.php');

$input = fread(STDIN, 32000);

$decode = new Mail_mimeDecode($input, "\r\n");
$structure = $decode->decode();

$gesucht		= array('message-id', 'from', 'in-reply-to', 'references', 'subject');

echo("--- Datenbankrelevante Felder ---\n");
foreach($gesucht as $was) {
	echo($structure->headers[$was]."\n");
}

?>