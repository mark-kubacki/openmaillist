<h2>Mail Lists</h2>
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
		    <dt><a href="threads.php?lid=<?= $list['lid'] ?>" title="threads of"><?= $list['name'] ?></a></dt>
		    <dd><?= $list['description'] ?></dd>
		</dl>
	    </td>
	    <td><?= $list['threads'] ?></td>
	    <td><?= $list['posts'] ?></td>
	    <td><?= $list['lastdate'] ?></td>
	</tr>
    <?php } ?>
</table>