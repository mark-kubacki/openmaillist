<h2>Threads</h2>
<table>
    <tr>
	<th>Subject</th>
	<th>Posts</th>
	<th>From</th>
	<th>Last Post</th>
    </tr>
    <?php foreach($threads as $thread) { ?>
	<tr>
	    <td><a href="messages.php?tid=<?= $thread['tid'] ?>" title="messages of"><?= $thread['name'] ?></a></td>
	    <td><?= $thread['posts'] ?></td>
	    <td><?= htmlspecialchars($thread['lastfrom']) ?></td>
	    <td><?= $thread['lastdate'] ?></td>
	</tr>
    <?php } ?>
</table>