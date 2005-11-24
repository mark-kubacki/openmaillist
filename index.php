<?php
include('./inc/_prepend.php');

// ------------------------------ Mailing Lists ---------------------------------------------------

// DATA
$lists = $oml->get_lists();

// DISPLAY
include('./templates/'.$cfg['theme'].'/listview.tpl');

include('./inc/_append.php');
?>