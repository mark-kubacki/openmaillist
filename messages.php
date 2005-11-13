<?php
include('inc/_prepend.php');

// ------------------------------ Messages --------------------------------------------------------

// DATA
$messages = $oml->get_messages($_GET['tid']);

// DISPLAY
include('templates/'.$cfg['theme'].'/messagesview.tpl');

include('inc/_append.php');
?>