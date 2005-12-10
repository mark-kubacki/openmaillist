<?php
include('./inc/_prepend.php');

// ------------------------------ Messages --------------------------------------------------------

// DATA
$thread		= $oml->get_thread($_GET['tid']);
$list		= $thread->get_owning_list();
$messages	= $thread->get_messages();

// DISPLAY
include('./templates/'.$cfg['theme'].'/messagesview.tpl');

include('./inc/_append.php');
?>