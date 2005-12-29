<?php
include('./inc/_prepend.php');

// DATA
$thread		= $oml->get_thread($_GET['tid']);
$thread->inc_views();
$list		= $thread->get_owning_list();
$messages	= $thread->get_messages();

// DISPLAY
include('./templates/'.$cfg['theme'].'/common-header.tpl');
include('./templates/'.$cfg['theme'].'/messagesview.tpl');
include('./templates/'.$cfg['theme'].'/common-footer.tpl');
include('./inc/_append.php');
?>