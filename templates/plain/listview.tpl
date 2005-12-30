<h2>Mailing Lists</h2>
<table>
	<tr>
		<th>Name</th>
		<th>Threads</th>
		<th>Posts</th>
		<th>Last Post</th>
	</tr>
	<?php foreach($lists as $list) { ?>
		<tr>
			<td>
				<dl>
					<dt><a href="threads.php?lid=<?= $list->get_unique_value() ?>" title="threads of"><?= $list->get_name() ?></a></dt>
					<dd><?= $list->get_description() ?></dd>
					<dd>
						write to <a href="mailto:<?= EncodeEmail($list->get_address()) ?>" title="lists' email"><?= EncodeEmail($list->get_address()) ?></a>;
						<a href="rss.php?lid=<?= $list->get_unique_value() ?>" class="rss" title="This is a link to this list's RSS channel: <?= $list->get_name() ?>">
							RSS feed <cite class="rss">XML</cite>
						</a>
					</dd>
				</dl>
			</td>
			<td class="fig"><?= $list->number_of_threads() ?></td>
			<td class="fig"><?= $list->number_of_messages() ?></td>
			<td class="last_post">
				<?php if($list->number_of_messages() > 0) { ?>
					<?php $post = $list->get_last_message(); ?>
					<ul class="post_def">
						<li><strong>on</strong> <?= $post->get_date_received($cfg['display']['date_format']) ?></li>
						<li class="nh"><strong>by</strong> <?= $post->get_author() ?></li>
						<?php $thread = $post->get_owning_thread(); ?>
						<li class="nh"><strong>in</strong> &bdquo;<a href="messages.php?tid=<?= $thread->get_unique_value() ?>" title="messages of"><?= $thread->get_name() ?></a>&rdquo;</li>
					</ul>
				<?php } else { ?>
					never
				<?php } ?>
			</td>
		</tr>
	<?php } ?>
</table>