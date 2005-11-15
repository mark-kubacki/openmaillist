#!/bin/env php
<?php
define(MODE_HEADER,	1);
define(MODE_BODY,	2);
define(MODE_ATT,	4);

$i		= 0;
$mode	= MODE_HEADER;

$header	= '';
$body	= '';
$attachement	= array();
$fields	= array();

while(!feof(STDIN)) {
	$line = fgets(STDIN, 1024);
	switch($mode) {
		case MODE_HEADER:
				if(trim($line) == '') {
					$mode	= MODE_BODY;
					preg_match_all('/([\w\-]+):\s([^\n]+(?:\n\s+[^\n]+)*)/', $header, $arr);
				}
				else {
					$header	.= $line;
				}
			break;
		case MODE_BODY:
			$body .= $line;
			break;
		case MODE_ATT:
			break;
	}
}

$gesucht		= array('Message-ID', 'From', 'In-Reply-To', 'References', 'Subject');

echo("--- Datenbankrelevante Felder ---\n");
foreach($gesucht as $was) {
	$i = array_search($was, $arr[1]);
	echo($arr[1][$i]." : ".$arr[2][$i]."\n");
}
echo("--- Body ---");
echo($body);

?>