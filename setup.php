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

// threads' table
switch($factory->create_threads_table()) {
	case '1':	echo('Table already exists.');	break;
	case '2':	echo('Table created.');		break;
}
echo('<br />');

// some example messages
//$db->debug	= true;
switch($factory->create_messages_table()) {
	case '1':	echo('Table already exists.');	break;
	case '2':	echo('Table created.');		break;
}
echo('<br />');
$todo	= array(
		  array('message_id'		=> '200510281112.13579.alex@noligy.de',
			'lid'			=> 3,
			'datesend'		=> 1130490761,
			'datereceived'		=> 1130490762,
			'sender'		=> '<alex@noligy.de>',
			'subject'		=> 'Mouserad unter Linux',
			'hasattachements'	=> 1,
			'msgtext'		=> <<<EOT
Hi Mark,

hiermit sollte es gehen:


Section "InputDevice"
        Identifier      "Configured Mouse"
        Driver          "mouse"
        Option          "CorePointer"
        Option          "Device"                "/dev/input/mice"
        Option          "Protocol"              "ImPS/2"
        Option          "ZAxisMapping"          "4 5"
EOT
			),
		  array('message_id'		=> '20051101205153.GA891@ds217-115-141-141.dedicated.hosteurope.de',
			'lid'			=> 1,
			'datesend'		=> 1130878268,
			'datereceived'		=> 1130880023,
			'sender'		=> 'Jochen Suckfuell <boger@suckfuell.net>',
			'subject'		=> 'Mailbox names limited to 16 chars',
			'hasattachements'	=> 0,
			'msgtext'		=> <<<EOT
Hello!

Is there a technical reason that the mailbox names are limited to 16
characters by openmailadmin?
If not, I would replace "16" with my desired max length everywhere (including
database.sql, before creating tables) and be happy.

Bye
Jochen Suckfüll
EOT
			),
		  array('message_id'		=> '43692E64.5010708@hurrikane.de',
			'lid'			=> 1,
			'in_reply_to'		=> '20051101205153.GA891@ds217-115-141-141.dedicated.hosteurope.de',
			'datesend'		=> 1130966585,
			'datereceived'		=> 1130966656,
			'sender'		=> 'W-Mark Kubacki <wmark@hurrikane.de>',
			'subject'		=> 'Re: Mailbox names limited to 16 chars',
			'hasattachements'	=> 0,
			'msgtext'		=> <<<EOT
Hallo,

an older IMAP server had a limitation of 16 characters in mailbox names.

If you use something recent you can freely reset that limitation - indeed, I have already made these limits (upper and lower) configurable. See also [1].

(Unless an installer is made which can query for limits you still are to modify database.sql, too.)


Gruß,

W-Mark Kubacki

[1] http://www.openmailadmin.org/changeset/138
EOT
			),
		);
foreach($todo as $task) {
	$myMsg		= $factory->get_message();
	$myMsg->let($task['message_id'], $task['datesend'], $task['datereceived'], $task['sender'], $task['subject'], $task['hasattachements'], $task['msgtext']);

	// register that message
	$theList	= $factory->get_list($task['lid']);
	$theList->register_message($myMsg);

	// write it to db
	if(!$myMsg->write_to_db()) {
		echo('Argh!');
	}
	echo('<br />');
}

include('./inc/_append.php');
?>