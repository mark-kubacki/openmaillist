<h2>Mail Lists</h2>
<table>
    <tr>
	<th>Name</th>
	<th>Threads</th>
	<th>Posts</th>
	<th>Last Post</th>
    </tr>
    <?php foreach($factory->get_all_lists() as $list) { ?>
	<tr>
	    <td>
		<dl>
		    <dt><a href="threads.php?lid=<?= $list->get_unique_value() ?>" title="threads of"><?= $list->get_name() ?></a></dt>
		    <dd><?= $list->get_description() ?></dd>
		</dl>
	    </td>
	    <td class="fig"><?= $list->number_of_threads() ?></td>
	    <td class="fig"><?= $list->number_of_messages() ?></td>
	    <td class="date">
		<?php if($list->number_of_messages() > 0) { ?>
			<?php $post = $list->get_last_message('datereceived'); ?>
			<?= $post->get_date_received($cfg['display']['date_format']) ?>
		<?php } else { ?>
			never
		<?php } ?>
	    </td>
	</tr>
    <?php } ?>
</table>