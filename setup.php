<?php
include('./inc/_prepend.php');

switch(oml_list::create_your_table($db, 'MyLists4')) {
	case '1':	echo('Table already exists.');	break;
	case '2':	echo('Table created.');		break;
}
echo('<br />');
switch(oml_message::create_your_table($db, 'MyMsg1')) {
	case '1':	echo('Table already exists.');	break;
	case '2':	echo('Table created.');		break;
}

include('./inc/_append.php');
?>