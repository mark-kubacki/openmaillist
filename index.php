<?php
include('./inc/_prepend.php');
include('./templates/'.$cfg['theme'].'/common-header.tpl');

// ------------------------------ Mailing Lists ---------------------------------------------------

// DATA
$lists = $oml->get_all_lists();

// DISPLAY
include('./templates/'.$cfg['theme'].'/listview.tpl');

include('./templates/'.$cfg['theme'].'/common-footer.tpl');
include('./inc/_append.php');
?>