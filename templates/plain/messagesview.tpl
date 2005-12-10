<h2>Messages</h2>
<ol class="messages">
<?php foreach($messages as $msg) { ?>
<li class="message">
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
	<pre><blockquote><?= $msg->get_text() ?></blockquote></pre>
    </div>
</li>
<?php } ?>
</ol>