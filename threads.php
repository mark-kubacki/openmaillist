<?php
include('inc/_prepend.php');

// ------------------------------ Threads ---------------------------------------------------------

// DATA
$lists = $oml->get_threads($_GET['lid']);

// DISPLAY
include('templates/'.$cfg['theme'].'/threadview.tpl');

include('inc/_append.php');
?>