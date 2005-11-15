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

while(!feof(STDIN) && $i < 50) {
	$line = fgets(STDIN, 1024);
	switch($mode) {
		case MODE_HEADER:
				if(trim($line) == '') {
					$mode	= MODE_BODY;
				}
				else {
					// ([\w\-]+):\s([^\n]+(?:\n\s+[^\n]+)*)
					switch($line{0}) {
						case 'S':
							if(preg_match('/Subject:\s(.*)/', $line, $arr)) {
								$fields['Subject'] = $arr[1];
							}
							break;
						case 'M':
							if(preg_match('/Message-ID:\s<(.*)>/', $line, $arr)) {
								$fields['Message-ID'] = $arr[1];
							}
							break;
						case 'F':
							if(preg_match('/From:\s(.*)/', $line, $arr)) {
								$fields['From'] = $arr[1];
							}
							break;
						case 'I':
							if(preg_match('/In-Reply-To:\s<(.*)>/', $line, $arr)) {
								$fields['In-Reply-To'] = $arr[1];
							}
						case 'R':
							if(preg_match('/References:\s<(.*)>/', $line, $arr)) {
								$fields['References'] = $arr[1];
							}
					}
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

echo("--- Datenbankrelevante Felder ---");
print_r($fields);
echo("--- Body ---");
echo($body);

?>