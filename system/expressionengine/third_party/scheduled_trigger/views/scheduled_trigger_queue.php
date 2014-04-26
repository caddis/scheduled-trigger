<p>If you have updated settings or need to reset the queue for any reason <a href="<?=$reinitialize_url;?>">click here</a>.</p>
<table cellpadding="0" cellspacing="0" class="mainTable">
	<thead>
		<tr>
			<th width="25%">#</th>
			<th width="25%">Title</th>
			<th width="25%">Type</th>
			<th width="25%">Date</th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($queue) === 0) { ?>
		<tr>
			<td colspan="4">No queued entries available.</td>
		</tr>
		<?php } else { ?>
		<?php foreach ($queue as $row) { ?>
		<tr>
			<td><?=$row['entry_id'] ?></td>
			<td><a href="<?=BASE; ?>&amp;C=content_publish&amp;M=entry_form&amp;entry_id=<?=$row['entry_id']; ?>"><?=$row['title']; ?></a></td>
			<td><?=$row['type'] ?></td>
			<td><?=$row['date'] ?></td>
		</tr>
		<?php } ?>
		<?php } ?>
	</tbody>
</table>