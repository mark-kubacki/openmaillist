<h2>Threads</h2>
<div class="path">
	<a href="index.php">Mailing Lists</a>&nbsp;&raquo;&nbsp;<?= $list->get_name() ?>
</div>
<table class="threads">
	<tr>
		<th class="subj">Subject</th>
		<th>Posts</th>
		<th>Views</th>
		<th>Author</th>
		<th>Last Post</th>
	</tr>
	<?php foreach($threads as $thread) { ?>
		<tr>
			<td class="subj"><a href="messages.php?tid=<?= $thread->get_unique_value() ?>" title="messages of"><?= $thread->get_name() ?></a></td>
			<td class="fig"><?= $thread->number_of_messages() ?></td>
			<td class="fig"><?= $thread->get_views() ?></td>
			<td><?= htmlentities($thread->get_first_message()->get_author()) ?></td>
			<?php $last_msg = $thread->get_last_message(); ?>
			<td class="date">
				<?= $last_msg->get_date_received($cfg['display']['date_format']) ?>
				by <?= htmlentities($last_msg->get_author()) ?>
			</td>
		</tr>
	<?php } ?>
</table>
<div class="rss">
	<a href="rss.php?lid=<?= $list->get_unique_value() ?>" class="rss" title="This is a link to this list's RSS channel: <?= $list->get_name() ?>">
		<img src="<?= $cfg['images_dir'] ?>/xml.gif" border="0" alt="XML" title="XML logo" /> RSS feed
	</a>
</div>