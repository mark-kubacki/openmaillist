<?php
$i = 0;
$cfg['Servers']['verbose'][] = 'localhost';
$cfg['Servers']['number'][] = $i++;
$cfg['Servers']['DB'][] = array(
	'TYPE'	=> 'mysql',			// currently only mysql
	'HOST'	=> 'localhost',
	'USER'	=> '##MysqlUser##',
	'PASS'	=> '##MysqlSecret##',
	'DB'	=> '##MysqlDB##',
	'PREFIX'	=> ''			// prefix to table names
);
?>