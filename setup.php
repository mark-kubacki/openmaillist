<?php
include('./inc/_prepend.php');

// lists' table
switch($factory->create_lists_table()) {
	case '1':	echo('Table already exists.');	break;
	case '2':	echo('Table created.');		break;
}
// some example lists
$todo	= array(array('openmailadmin', 'list@openmailadmin.org', 'Everything about openmailadmin.'),
		array('openmaillist', 'list@openmaillist.org', 'Do you enjoy the great product of Alex and Mark? Words of praise go here.'),
		array('Noligy\'s Exchange', 'exchange@noligy.de', 'Ich <b>liebe</b> Möpse. Leider <i>vertragen</i> sie sich nicht mit Schnauzern.'),
		);
foreach($todo as $task) {
	$myList	= $factory->get_list();
	$myList->set_name($task[0]);
	$myList->set_address($task[1]);
	$myList->set_description($task[2]);
	$myList->write_to_db();
}
echo('<br />');

// messages' table
switch($factory->create_messages_table()) {
	case '1':	echo('Table already exists.');	break;
	case '2':	echo('Table created.');		break;
}

include('./inc/_append.php');
?>