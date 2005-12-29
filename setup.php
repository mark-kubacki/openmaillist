<?php
die('Uncomment this for setting up OML. After having done so, restore this line.');

include('./inc/_prepend.php');
include('./templates/'.$cfg['theme'].'/common-header.tpl');

echo('<h2>Setup</h2>');
echo('<h3>creation of tables</h3>');
// lists' table
switch($factory->create_lists_table()) {
	case '1':	echo('Table already exists.');	break;
	case '2':	echo('Table created.');		break;
}
// some example lists
$todo	= array(array('mylist', 'list@example.com', 'Please address your issues to this list.'),
		array('mytest', 'test@example.com', 'Write to this list if you jsut want to test arrival of your messages.'),
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

// messages' table
switch($factory->create_messages_table()) {
	case '1':	echo('Table already exists.');	break;
	case '2':	echo('Table created.');		break;
}
echo('<br />');

// now insert example messages
echo('<h3>example messages</h3>');
try {
	$myList = $factory->get_list_by_name('mylist');
	$todo	= array('./testdata/1.',
			'./testdata/2.',
			'./testdata/3.',
			);
	foreach($todo as $filename) {
		$email	= new oml_email(file_get_contents($filename));
		$oml->put_email($myList, $email);
	}
} catch (Exception $e) {
	echo('Some example messages could not be inserted into list <cite>mylist</cite>:');
	echo('<i>'.$e->getMessage().'</i>');
}

include('./templates/'.$cfg['theme'].'/common-footer.tpl');
include('./inc/_append.php');
?>