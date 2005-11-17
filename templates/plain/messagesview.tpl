<h2>Messages</h2>
<ol class="messages">
<?php foreach($messages as $msg) { ?>
<li class="message">
    <div class="header">
	<dl>
	    <dt>Sender:</dt>
	    <dd><?= htmlspecialchars($msg['sender']) ?></dd>
	    <dt>Received:</dt>
	    <dd><?= $msg['datereceived'] ?><dd>
	    <dt>Subject:</dt>
	    <dd><?= $msg['subject'] ?><dd>
	</dl>
    </div>
    <div class="body">
	<pre><blockquote><?= $msg['body'] ?></blockquote></pre>
    </div>
    <?php if(count($msg['attach']) > 0) { ?>
	<div class="attachement">
	    <ul>
	    <?php foreach($msg['attach'] as $attachement) { ?>
		<li>
		    <a href="<?= $cfg['upload_dir'].$attachement['Location'] ?>" title="attachement">
			<?= $attachement['Location'] ?>
		    </a>
		</li>
	    <?php } ?>
	    </ul>
	</div>
    <?php } ?>
</li>
<?php } ?>
</ol>