<?php
include('./inc/_prepend.php');

// ------------------------------ Threads ---------------------------------------------------------

// DATA
$list		= $oml->get_list($_GET['lid']);
$threads	= $list->get_threads();

// DISPLAY
include('./templates/'.$cfg['theme'].'/threadview.tpl');

include('./inc/_append.php');
?>