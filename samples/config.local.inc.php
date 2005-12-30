<?php
// copy this file into /inc/ and modify it

$cfg['AbsoluteUri']	= 'http://127.0.0.1/openmaillist/';

$i = 0;
$cfg['Servers']['verbose'][] = 'localhost';
$cfg['Servers']['number'][] = $i++;
$cfg['Servers']['DB'][] = array(
	'TYPE'	=> 'mysql',
	'HOST'	=> 'localhost',
	'USER'	=> '##MysqlUser##',
	'PASS'	=> '##MysqlSecret##',
	'DB'	=> '##MysqlDB##',
	'PREFIX'	=> ''			// prefix to table names
);
?>