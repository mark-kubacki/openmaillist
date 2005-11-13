<?php
include('inc/_prepend.php');

// ------------------------------ Threads ---------------------------------------------------------

// DATA
$threads = $oml->get_threads($_GET['lid']);

// DISPLAY
include('templates/'.$cfg['theme'].'/threadview.tpl');

include('inc/_append.php');
?>