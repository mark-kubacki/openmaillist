<h2>Threads</h2>
<div class="path">
	<a href="index.php">Mailing Lists</a>&nbsp;&raquo;&nbsp;<?= $list->get_name() ?>
</div>
<table class="threads">
    <tr>
	<th>Subject</th>
	<th>Posts</th>
	<th>From</th>
	<th>Last Post</th>
    </tr>
    <?php foreach($threads as $thread) { ?>
	<tr>
	    <td><a href="messages.php?tid=<?= $thread->get_unique_value() ?>" title="messages of"><?= $thread->get_name() ?></a></td>
	    <td class="fig"><?= $thread->number_of_messages() ?></td>
	    <?php $post = $thread->get_last_message(); ?>
	    <td><?= htmlentities($post->get_author()) ?></td>
	    <td class="date"><?= $post->get_date_received($cfg['display']['date_format']) ?></td>
	</tr>
    <?php } ?>
</table>