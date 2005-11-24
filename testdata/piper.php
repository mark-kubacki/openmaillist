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
// Daten herausfinden
foreach($structure->headers['received'] as $rec_raw) {
	$zeit_raw = substr(strrchr($rec_raw, ';'), 2);
	$zeit_nix = strtotime($zeit_raw);
	$zeit_rev = date('r', $zeit_nix);
	echo("$zeit_raw\t\t->\t$zeit_nix\t->\t$zeit_rev\n");
}

// echo("\n\n--- gesamt ---\n");
// print_r($structure->headers);

?>