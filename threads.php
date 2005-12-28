<?php
include('./inc/_prepend.php');
include('./templates/'.$cfg['theme'].'/common-header.tpl');

// ------------------------------ Threads ---------------------------------------------------------

// DATA
$list		= $oml->get_list($_GET['lid']);
$threads	= $list->get_threads();

// DISPLAY
include('./templates/'.$cfg['theme'].'/threadview.tpl');

include('./templates/'.$cfg['theme'].'/common-footer.tpl');
include('./inc/_append.php');
?>