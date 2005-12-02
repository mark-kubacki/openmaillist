#!/bin/env php
<?php
include('../inc/lib/oml_email.php');

$input = fread(STDIN, 32000);

$myEmail = new oml_email($input);
/*
$myEmail->study();

echo('--- gesamt ---');
echo("\n");
print_r($myEmail->hoi);

if($myEmail->has_attachements()) {
	echo("Hat Anhänge.\n");
	$myEmail->split_parts();
	print_r($myEmail->parts);
}
else {
	echo("Hat keine Anhänge.\n");
}
*/

$myEmail->set_attachement_storage('/tmp');
$myEmail->get_first_displayable_part();
print_r($myEmail->get_attachements());

?>