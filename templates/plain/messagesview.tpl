<h2>Messages</h2>
<div class="path">
	<a href="index.php">Mailing Lists</a>&nbsp;&raquo;&nbsp;<a href="threads.php?lid=<?= $list->get_unique_value() ?>"><?= $list->get_name() ?></a>&nbsp;&raquo;&nbsp;<?= $thread->get_name() ?>
</div>
<ol class="messages">
<?php foreach($messages as $msg) { ?>
<li class="message" id="mid<?= $msg->get_unique_value() ?>">
	<div class="header">
		<dl>
			<dt>Sender:</dt>
			<dd>
				<a href="mailto:<?= EncodeEmail($list->get_address()) ?>?subject=<?= rawurlencode($msg->get_subject()) ?>&amp;in-reply-to=<?= rawurlencode($msg->get_message_id()) ?>" title="respond to this message by email">
					<?= htmlentities($msg->get_author()) ?>
				</a>
			</dd>
			<dt>Received:</dt>
			<dd><?= $msg->get_date_received($cfg['display']['date_format']) ?></dd>
			<dt>Subject:</dt>
			<dd><?= $msg->get_subject() ?></dd>
		</dl>
	</div>
	<div class="body">
		<blockquote><pre><?= format_quotings(htmlentities($msg->get_text())) ?></pre></blockquote>
	</div>
<?php if($msg->has_attachments()) { ?>
	<div class="attachment">
		<ol>
		<?php foreach($msg->get_attachments() as $attachment) { ?>
			<li>
				<a href="<?= $cfg['upload_dir'].$attachment->get_storage_name() ?>" title="attachment"><?= $attachment->get_filename() ?></a>
			</li>
		<?php } ?>
		</ol>
	</div>
<?php } ?>
</li>
<?php } ?>
</ol>