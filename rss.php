<?php
include('./inc/_prepend.php');

// DATA
header('Content-Type: text/xml');
$list		= $oml->get_list($_GET['lid']);
$messages	= $list->get_num_latest_entries($cfg['rss']['num_messages']);

if(is_array($messages) && count($messages) > 0) {
	header('Last-Modified: '.$messages[count($messages)-1]->get_date_received('r'));
}
// DISPLAY
include('./templates/rss_2.0.tpl');

include('./inc/_append.php');
?>