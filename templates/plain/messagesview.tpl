<h2>Messages</h2>
<ol>
<?php foreach($messages as $msg) { ?>
<li>
    <div>
	<dl>
	    <dt>Sender: </dt>
	    <dd><?= htmlspecialchars($msg['sender']) ?></dd>
	    <dt>Subject: </dt>
	    <dd><?= $msg['subject'] ?><dd>
	    <dt>Received: </dt>
	    <dd><?= $msg['datereceived'] ?><dd>
	</dl>
    </div>
    <div>
	<pre><?= $msg['body'] ?></pre>
    </div>
    <?php if(count($msg['attach']) > 0) { ?>
	<div>
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