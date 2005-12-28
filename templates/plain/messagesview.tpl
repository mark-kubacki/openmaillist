<h2>Messages</h2>
<div class="path">
	<a href="index.php">Mailing Lists</a>&nbsp;&raquo;&nbsp;<a href="threads.php?lid=<?= $list->get_unique_value() ?>"><?= $list->get_name() ?></a>&nbsp;&raquo;&nbsp;<?= $thread->get_name() ?>
</div>
<ol class="messages">
<?php foreach($messages as $msg) { ?>
<li class="message" id="<?= $msg->get_unique_value() ?>">
    <div class="header">
	<dl>
	    <dt>Sender:</dt>
	    <dd><?= htmlentities($msg->get_author()) ?></dd>
	    <dt>Received:</dt>
	    <dd><?= $msg->get_date_received($cfg['display']['date_format']) ?><dd>
	    <dt>Subject:</dt>
	    <dd><?= $msg->get_subject() ?><dd>
	</dl>
    </div>
    <div class="body">
	<pre><blockquote><?= format_quotings(htmlentities($msg->get_text())) ?></blockquote></pre>
    </div>
</li>
<?php } ?>
</ol>